<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardLoginValidator login form post validator.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage validator
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardLoginValidator extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('email_field', 'email');
    $this->addOption('password_field', 'password');
    $this->addOption('throw_global_error', false);
  }

  protected function doClean($values)
  {
    $email    = isset($values[$this->getOption('email_field')])
                  ? $values[$this->getOption('email_field')]
                  : '';
    $password = isset($values[$this->getOption('password_field')])
                  ? $values[$this->getOption('password_field')]
                  : '';

    if ($user = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail($email))
    {
      if ($user->getIsActive() && $user->checkPassword($password))
      {
        return array_merge($values, array('user' => $user));
      }
    }

    $this->setMessage('invalid', 'Wrong password or email address.');

    throw new sfValidatorErrorSchema(
      $this,
      array(
        $this->getOption('global') => new sfValidatorError($this, 'invalid')
      )
    );
  }
}
