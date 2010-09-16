<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasesfObjectGuardInviteAction implements invite action.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage actions
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class BasesfObjectGuardInviteAction extends sfObjectGuardPasswordAction
{
  protected function getInviteForm()
  {
    $class = sfConfig::get('app_sf_object_guard_plugin_invite_form', 'sfObjectGuardInviteForm');

    return new $class;
  }

  public function execute($request)
  {
    $inviter = $this->getUser()->getGuardUser();
    $this->redirectIf(is_null($inviter), '@homepage');

    $this->form = $this->getInviteForm();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        // generate temporary password
        $password = $this->generateTemporaryPassword();

        // save new user
        $this->form->setPassword($password);
        $user = $this->form->save();

        // invite key generation
        $inviteKey = $this->generateActivationKey('invite');
        $inviteKey->setInviter($inviter);
        $inviteKey->setUser($user);
        $inviteKey->save();

        // invite sending
        $this->getMailer()->send($this->getActivationMailMessage(
          $user, $inviteKey->getActivationKey(), $password
        ));

        // if we not in dev environment - redirect
        if ('dev' !== sfConfig::get('sf_environment'))
        {
          $this->getUser()->setFlash('notice',
            $this->getPartial('mailSentFlash', array('user' => $user))
          );
          $this->redirect($this->generateUrl('sf_object_guard_invite'));
        }
      }
    }
  }
}
