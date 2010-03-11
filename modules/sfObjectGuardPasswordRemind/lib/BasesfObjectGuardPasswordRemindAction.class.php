<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasesfObjectGuardPasswordRemindAction implements password remind action.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage actions
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class BasesfObjectGuardPasswordRemindAction extends sfObjectGuardPasswordAction
{
  public function execute($request)
  {
    $this->form = new sfObjectGuardReminderForm;

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $values = $this->form->getValues();
        $user = $values['user'];

        // generate temporary password
        $password = $this->generateTemporaryPassword();

        // password key generation
        $passwordKey = $this->generateActivationKey('password');
        $passwordKey->setUser($user);
        $passwordKey->setAdditional($password);
        $passwordKey->save();

        // remind key sending
        $this->getMailer()->send($this->getActivationMailMessage(
          $user->getEmail(), $passwordKey->getActivationKey(), $password
        ));

        $this->getUser()->setFlash('notice', $this->getPartial('mailSentFlash'));

        // if we not in dev environment - redirect
        if ('dev' != sfConfig::get('sf_environment'))
        {
          $this->redirect($this->generateUrl('sf_object_guard_password_remind'));
        }
      }
    }
  }
}
