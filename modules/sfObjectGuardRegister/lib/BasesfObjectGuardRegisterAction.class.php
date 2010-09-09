<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasesfObjectGuardRegisterAction implements register action.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage actions
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class BasesfObjectGuardRegisterAction extends sfObjectGuardPasswordAction
{
  protected function getRegisterForm()
  {
    $class = sfConfig::get('app_sf_object_guard_plugin_register_form', 'sfObjectGuardRegisterForm');

    return new $class;
  }

  public function execute($request)
  {
    $this->form = $this->getRegisterForm();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        // generate temporary password
        $password = $this->generateTemporaryPassword();

        // set temporary password & save user
        $this->form->setPassword($password);
        $user = $this->form->save();
        $user->postRegister();

        // activation key generation
        $activationKey = $this->generateActivationKey('register');
        $activationKey->setUser($user);
        $activationKey->save();

        // activation key sending
        $this->getMailer()->send($this->getActivationMailMessage(
          $user->getEmail(), $activationKey->getActivationKey(), $password
        ));

        // if we not in dev environment - redirect
        if ('dev' !== sfConfig::get('sf_environment'))
        {
          $this->getUser()->setFlash('notice',
            $this->getPartial('mailSentFlash', array('user' => $user))
          );
          $this->redirect($this->generateUrl('sf_object_guard_register'));
        }
      }
    }
  }
}
