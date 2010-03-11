<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardPasswordAction does things.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage actions
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
abstract class sfObjectGuardPasswordAction extends sfAction
{
  protected function generateActivationKey($type)
  {
    $key = new sfObjectGuardActivationKey;
    $key->setKeyType(
      Doctrine::getTable('sfObjectGuardActivationKeyType')->findOneByName($type)
    );

    return $key;
  }

  protected function generateTemporaryPassword($length = 8)
  {
    $password = "";
    $possible = "0123456789abcdfghjkmnpqrstvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $i = 0;
    while ($i < $length)
    {
      $char = substr($possible, mt_rand(0, strlen($possible)-1), 1);
      if (!strstr($password, $char))
      {
        $password .= $char;
        $i++;
      }
    }
    return $password;
  }

  protected function getActivationMailMessage($email, $activation, $password)
  {
    return Swift_Message::newInstance()
      ->setFrom(
        sfConfig::get(
          'app_robot_mail_address',
          'robot@' . sfConfig::get('app_site_name', 'site')
        )
      )
      ->setTo($email)
      ->setSubject(
        $this->getPartial('mailNotificationSubject')
      )
      ->setBody(
        $this->getPartial('mailNotificationBody', array(
          'email'     => $email,
          'key'       => $activation,
          'password'  => $password
        ))
      )
    ;
  }
}