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
class BasesfObjectGuardRegisterAction extends sfAction
{
  public function execute($request)
  {
    $this->form = new sfObjectGuardRegisterForm;

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $user = $this->form->save();

        // activation key generation
        $activationKey = new sfObjectGuardActivationKey;
        $activationKey->setKeyType(
          Doctrine::getTable('sfObjectGuardActivationKeyType')->findOneByName('register')
        );
        $activationKey->setUser($user);
        $activationKey->save();

        // activation key sending
        $this->getMailer()->composeAndSend(
          sfConfig::get('app_robot_mail_address'),
          $user->getEmail(),
          $this->getContext()->getI18N()->__('Account activation for %1%.', array(
            '%1%' => sfConfig::get('app_site_name', 'site')
          )),
          $this->getPartial('mailNotificationBody', array('key' => $activationKey->getActivationKey()))
        );

        $this->getUser()->setFlash('notice',
          'We have successfully sent activation key to your email.'
        );
        $this->redirect($this->generateUrl('sf_object_guard_register'));
      }
    }
  }
}
