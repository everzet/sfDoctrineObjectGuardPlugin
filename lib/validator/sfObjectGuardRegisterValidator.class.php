<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardRegisterValidator register form post validator.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage form
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardRegisterValidator extends sfValidatorBase
{
  public function configure($options = array(), $messages = array())
  {
    $this->addOption('email_field', 'email');
    $this->addOption('throw_global_error', false);
  }

  protected function doClean($values)
  {
    $email = isset($values[$this->getOption('email_field')])
                  ? $values[$this->getOption('email_field')]
                  : '';

    if (Doctrine::getTable('sfObjectGuardUser')->findOneByEmail($email))
    {
      $this->setMessage('invalid', 'User with such email address already registered');
      throw new sfValidatorErrorSchema(
        $this,
        array(
          'email' => new sfValidatorError($this, 'invalid')
        )
      );
    }

    return $values;
  }
}
