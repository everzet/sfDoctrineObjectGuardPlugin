<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardPasswordForm password change form.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage form
 * @author     Konstantin Kudryashov <ever.zet>
 * @version    1.0.0
 */
class sfObjectGuardPasswordForm extends PluginBasesfObjectGuardUserForm
{
  public function configure()
  {
    parent::configure();
    unset($this['email']);

    $this->widgetSchema->setLabels(array(
      'password' => 'Your new password', 
    ));

    $this->widgetSchema->setNameFormat('password[%s]');
  }
}