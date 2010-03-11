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
  public function execute($request)
  {
    $this->form = new sfObjectGuardRegisterForm;

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

        // activation key generation
        $activationKey = $this->generateActivationKey('register');
        $activationKey->setUser($user);
        $activationKey->save();

        // activation key sending
        $this->getMailer()->send($this->getActivationMailMessage(
          $user->getEmail(), $activationKey->getActivationKey(), $password
        ));

        $this->getUser()->setFlash('notice', $this->getPartial('mailSentFlash'));
        $this->redirect($this->generateUrl('sf_object_guard_register'));
      }
    }
  }
}
