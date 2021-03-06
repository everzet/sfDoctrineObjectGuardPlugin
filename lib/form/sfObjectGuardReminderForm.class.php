<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardReminderForm password remind form.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage form
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardReminderForm extends PluginBasesfObjectGuardUserForm
{
  public function configure()
  {
    parent::configure();
    unset($this['password']);

    $this->validatorSchema->setPostValidator(new sfObjectGuardReminderValidator);
    $this->widgetSchema->setNameFormat('reminder[%s]');
  }
}