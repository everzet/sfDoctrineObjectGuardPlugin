<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2010 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * PluginBasesfObjectGuardUserForm does things.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage forms
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class PluginBasesfObjectGuardUserForm extends BasesfObjectGuardUserForm
{
  public function configure()
  {
    $this->setWidgets(array(
      'email'     => new sfWidgetFormInput(array(
        'label'     => 'Your email address'
      )),
      'password'  => new sfWidgetFormInputPassword(array(
        'label'     => 'Your password'
      ))
    ));

    $this->setValidators(array(
      'email'     => new sfValidatorEmail(array(), array(
        'invalid'   => 'Wrong email address specified',
        'required'  => 'You must specify email'
      )),
      'password'  => new sfValidatorString(array(
        'min_length'  => 6
      ), array(  
        'min_length'  => 'Password length must be 6 symbols min',
        'required'    => 'You must specify password'
      ))
    ));
  }
}
