<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardSecurityUser does things.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage user
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardSecurityUser extends sfBasicSecurityUser
{
  const CREDENTIAL_TABLES_NAMESPACE = 'symfony/user/sfUser/credentialTables';
  const CREDENTIAL_GLOBAL_NAMESPACE = 'global';

  private $user = null;
  private $credentialsLoadedFor = array();

  public function getReferer($default)
  {
    $referer = $this->getAttribute('referer', $default);
    $this->getAttributeHolder()->remove('referer');

    return $referer;
  }

  public function setReferer($referer)
  {
    if (!$this->hasAttribute('referer'))
    {
      $this->setAttribute('referer', $referer);
    }
  }

  public function initialize(sfEventDispatcher $dispatcher, sfStorage $storage, $options = array())
  {
    parent::initialize($dispatcher, $storage, $options);
    $this->credentialsLoadedFor = $storage->read(self::CREDENTIAL_TABLES_NAMESPACE);
    if (!$this->isAuthenticated())
    {
      $this->credentialsLoadedFor = array();
    }
  }

  public function shutdown()
  {
    $this->storage->write(self::CREDENTIAL_TABLES_NAMESPACE, $this->credentialsLoadedFor);
    parent::shutdown();
  }

  public function clearCredentials()
  {
    $this->credentialsLoadedFor = array();
    parent::clearCredentials();
  }

  protected function getCredentialOfTable($table)
  {
    //return $table->getTableName();
    return $table->getComponentName();
  }

  protected function getTableOfCredential($credential)
  {
    //return Doctrine::getTable(Doctrine_Inflector::classify($credential));
    return Doctrine::getTable($credential);
  }

  public function hasCredential($credential, $useAnd = true)
  {
    if (!$this->getGuardUser())
    {
      return false;
    }

    if ($this->getGuardUser()->getIsSuperAdmin())
    {
      return true;
    }

    if (!is_array($credential))
    {
      $credentialParts = explode('/', $credential);
      if (1 == count($credentialParts))
      {
        $credentialParts = array(self::CREDENTIAL_GLOBAL_NAMESPACE, $credentialParts[0]);
      }

      if (self::CREDENTIAL_GLOBAL_NAMESPACE == $credentialParts[0])
      {
        $table = Doctrine::getTable('sfObjectGuardUserGroup');
      }
      else
      {
        $table = $this->getTableOfCredential($credentialParts[0]);
      }

      if (!is_null($table))
      {
        if (!$this->isCredentialsLoadedForTable($table))
        {
          $this->loadCredentialsForTable($table);
        }
        if (3 == count($credentialParts))
        {
          $ownerCredential = $credentialParts[0] . '/' . $credentialParts[1] . '/owner';
          if (in_array($ownerCredential, $this->getCredentials()))
          {
            return true;
          }
        }
      }
      else
      {
        throw new sfException(sprintf('The model "%s" not found', $credentialParts[0]));
      }
    }

    return parent::hasCredential($credential, $useAnd);
  }

  public function hasCredentialForObject($credential, $object)
  {
    $tableName = $this->getCredentialOfTable($object->getTable());
    if ('sf_object_guard_user_group' == $tableName)
    {
      $credentialString = self::CREDENTIAL_GLOBAL_NAMESPACE . '/' .
        $object->getId() . '/' . $credential;
    }
    else
    {
      $credentialString = $tableName . '/' . $object->getId() . '/' . $credential;
    }

    return $this->hasCredential($credentialString);
  }

  public function isCredentialsLoadedForTable($table)
  {
    return in_array($this->getCredentialOfTable($table), $this->credentialsLoadedFor);
  }

  public function reloadCredentialsForTable($table)
  {
    $this->removeCredentialsForTable($table);
    $this->loadCredentialsForTable($table);
  }

  public function loadCredentialsForTable($table)
  {
    $this->addCredentials($table->getCredentialsForUser($this->getGuardUser()));
    $this->credentialsLoadedFor[] = $this->getCredentialOfTable($table);
  }

  public function removeCredentialsForTable($table)
  {
    foreach ($this->getCredentials() as $key => $value)
    {
      if (0 === stripos($value, $this->getCredentialOfTable($table)))
      {
        $this->removeCredential($value);
      }
    }
    unset($this->credentialsLoadedFor[$this->getCredentialOfTable($table)]);
  }

  public function isSuperAdmin()
  {
    return $this->getGuardUser() ? $this->getGuardUser()->getIsSuperAdmin() : false;
  }

  public function isAnonymous()
  {
    return !$this->isAuthenticated();
  }

  public function signIn($user, $remember = false, $con = null)
  {
    // signin
    $this->setAttribute('user_id', $user->getId(), 'sfObjectGuardSecurityUser');
    $this->setAuthenticated(true);
    $this->clearCredentials();

    // save last login
    $user->setLastLogin(date('Y-m-d h:i:s'));
    $user->save($con);

    // remember?
    if ($remember)
    {
      $expiration_age = sfConfig::get('app_sf_com_guard_plugin_remember_key_expiration_age', 15 * 24 * 3600);
      // remove old keys
      Doctrine_Query::create()
        ->delete()
        ->from('sfObjectGuardRememberKey k')
        ->where('created_at < ?', date('Y-m-d H:i:s', time() - $expiration_age))
        ->execute();

      // remove other keys from this user
      Doctrine_Query::create()
        ->delete()
        ->from('sfObjectGuardRememberKey k')
        ->where('k.user_id = ?', $user->getId())
        ->execute();

      // generate new keys
      $key = $this->generateRandomKey();

      // save key
      $rk = new sfObjectGuardRememberKey();
      $rk->setRememberKey($key);
      $rk->setsfObjectGuardUser($user);
      $rk->setIpAddress($_SERVER['REMOTE_ADDR']);
      $rk->save($con);

      // make key as a cookie
      $remember_cookie = sfConfig::get('app_sf_com_guard_plugin_remember_cookie_name', 'sfRemember');
      sfContext::getInstance()->getResponse()->setCookie($remember_cookie, $key, time() + $expiration_age);
    }
  }

  protected function generateRandomKey($len = 20)
  {
    $string = '';
    $pool   = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    for ($i = 1; $i <= $len; $i++)
    {
      $string .= substr($pool, rand(0, 61), 1);
    }

    return md5($string);
  }

  public function signOut()
  {
    $this->getAttributeHolder()->removeNamespace('sfObjectGuardSecurityUser');
    $this->user = null;
    $this->clearCredentials();
    $this->setAuthenticated(false);
    $expiration_age = sfConfig::get('app_sf_com_guard_plugin_remember_key_expiration_age', 15 * 24 * 3600);
    $remember_cookie = sfConfig::get('app_sf_com_guard_plugin_remember_cookie_name', 'sfRemember');
    sfContext::getInstance()->getResponse()->setCookie($remember_cookie, '', time() - $expiration_age);
  }

  public function getGuardUser()
  {
    if (!$this->user && $id = $this->getAttribute('user_id', null, 'sfObjectGuardSecurityUser'))
    {
      $this->user = Doctrine::getTable('sfObjectGuardUser')->find($id);

      if (!$this->user)
      {
        // the user does not exist anymore in the database
        $this->signOut();

        throw new sfException('The user does not exist anymore in the database.');
      }
    }

    return $this->user;
  }

  public function __toString()
  {
    return $this->getGuardUser() ? $this->getGuardUser()->__toString() : '';
  }

  public function getEmail()
  {
    return $this->getGuardUser() ? $this->getGuardUser()->getEmail() : null;
  }

  public function setPassword($password, $con = null)
  {
    if ($this->getGuardUser())
    {
      $this->getGuardUser()->setPassword($password);
      $this->getGuardUser()->save($con);
    }
  }

  public function checkPassword($password)
  {
    return $this->getGuardUser() ? $this->getGuardUser()->checkPassword($password) : null;
  }

  public function getProfile()
  {
    return $this->getGuardUser() ? $this->getGuardUser()->getProfile() : null;
  }
}
