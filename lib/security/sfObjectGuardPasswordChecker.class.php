<?php

class sfObjectGuardPasswordChecker
{
  static public function checkUserPassword(sfEvent $event)
  {
    $parameters = $event->getParameters();
    $user = sfContext::getInstance()->getUser();

    if (
         $user->isAuthenticated() &&
         is_null($user->getGuardUser()->getPassword()) &&
         !(
            'sfObjectGuardAuth' == $parameters['module'] &&
            'password' == $parameters['action']
          ) &&
         !(
            'sfObjectGuardAuth' == $parameters['module'] &&
            'logout' == $parameters['action']
          )
       )
    {
      self::forwardToPasswordAction();
    }
  }

  static protected function forwardToPasswordAction()
  {
    sfContext::getInstance()->getController()->redirect('sf_object_guard_password');

    throw new sfStopException();
  }
}
