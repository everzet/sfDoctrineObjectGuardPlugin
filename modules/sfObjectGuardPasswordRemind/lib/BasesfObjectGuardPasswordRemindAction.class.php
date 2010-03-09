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
class BasesfObjectGuardPasswordRemindAction extends sfAction
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

        // password key generation
        $passwordKey = new sfObjectGuardActivationKey;
        $passwordKey->setKeyType(
          Doctrine::getTable('sfObjectGuardActivationKeyType')->findOneByName('password')
        );
        $passwordKey->setUser($user);
        $passwordKey->save();

        // password key sending
        $this->getMailer()->composeAndSend(
          sfConfig::get('app_robot_mail_address'),
          $user->getEmail(),
          $this->getContext()->getI18N()->__('Password recovery for %1%.', array(
            '%1%' => sfConfig::get('app_site_name', 'site')
          )),
          $this->getPartial('mailNotificationBody', array('key' => $passwordKey->getActivationKey()))
        );

        $this->getUser()->setFlash('notice',
          'We have successfully sent password recovery instruction to your email.'
        );
        $this->redirect($this->generateUrl('sf_object_guard_password_remind'));
      }
    }
  }
}
