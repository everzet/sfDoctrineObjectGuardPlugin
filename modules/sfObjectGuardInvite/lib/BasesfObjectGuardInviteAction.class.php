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
class BasesfObjectGuardInviteAction extends sfAction
{
  public function execute($request)
  {
    $inviter = $this->getUser()->getGuardUser();
    $this->redirectIf(is_null($inviter), '@homepage');

    $this->form = new sfObjectGuardInviteForm;

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $user = $this->form->save();

        // invite key generation
        $inviteKey = new sfObjectGuardActivationKey;
        $inviteKey->setKeyType(
          Doctrine::getTable('sfObjectGuardActivationKeyType')->findOneByName('invite')
        );
        $inviteKey->setInviter($inviter);
        $inviteKey->setUser($user);
        $inviteKey->save();

        // invite sending
        $this->getMailer()->composeAndSend(
          sfConfig::get('app_robot_mail_address'),
          $user->getEmail(),
          $this->getContext()->getI18N()->__('Invite to the %1%.', array(
            '%1%' => sfConfig::get('app_site_name', 'site')
          )),
          $this->getPartial('mailNotificationBody', array('key' => $inviteKey->getActivationKey()))
        );

        $this->getUser()->setFlash('notice',
          'We have successfully sent registration instruction to the specified email.'
        );
        $this->redirect($this->generateUrl('sf_object_guard_invite'));
      }
    }
  }
}
