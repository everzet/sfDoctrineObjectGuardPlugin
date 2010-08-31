<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardReminderValidator reminder form validator.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage form
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardReminderValidator extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('email_field', 'email');
    $this->addOption('throw_global_error', false);
  }

  protected function doClean($values)
  {
    $email    = isset($values[$this->getOption('email_field')])
                  ? $values[$this->getOption('email_field')]
                  : '';

    if ($user = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail($email))
    {
      return array_merge($values, array('user' => $user));
    }

    $this->setMessage('invalid', 'No such user in our database');

    throw new sfValidatorErrorSchema(
      $this, array('email' => new sfValidatorError($this, 'invalid'))
    );
  }
}
