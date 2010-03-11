<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardBasicSecurityFilter does things.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage filters
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardBasicSecurityFilter extends sfBasicSecurityFilter
{
  protected $object;

  public function execute($filterChain)
  {
    $user = $this->getContext()->getUser();

    if ($this->isFirstCall() and !$user->isAuthenticated())
    {
      if ($cookie = $this->getCookie())
      {
        $q = Doctrine_Query::create()
              ->from('sfObjectGuardRememberKey r')
              ->innerJoin('r.sfObjectGuardUser u')
              ->where('r.remember_key = ?', $cookie);

        if ($q->count())
        {
          $user->signIn($q->fetchOne()->sfObjectGuardUser);
        }
      }
    }

    parent::execute($filterChain);
  }

  protected function getCookie()
  {
    return $this->getContext()->
      getRequest()->
      getCookie(sfConfig::get('app_sf_com_guard_plugin_remember_cookie_name', 'sfRemember'));
  }

  protected function getRouteObject()
  {
    if (is_null($this->object))
    {
      $route = $this->getContext()->getRequest()->getAttribute('sf_route');
      if ($route instanceof sfDoctrineRoute)
      {
        $this->object = $route->getObject();
      }
    }

    return $this->object;
  }

  protected function getUserCredential()
  {
    $credential = parent::getUserCredential();

    if (!is_null($this->getRouteObject()))
    {
      $credential = is_array($credential) ? $credential : array($credential);
      foreach ($credential as $key => $value)
      {
        $credential[$key] = str_ireplace('/id/', '/' . $this->getRouteObject()->getId() . '/', $value);
      }
    }

    return $credential;
  }
}