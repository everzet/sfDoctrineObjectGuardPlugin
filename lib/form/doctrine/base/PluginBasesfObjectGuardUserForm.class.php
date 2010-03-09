<?php

/**
 * Login form.
 *
 * @package    form
 * @subpackage User
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
        'invalid'   => 'Wrong email address specified.',
        'required'  => 'You must specify email.'
      )),
      'password'  => new sfValidatorString(array(
        'min_length'  => 6
      ), array(  
        'min_length'  => 'Password length must be 6 symbols min.',
        'required'    => 'You must specify password.'
      ))
    ));
  }
}
