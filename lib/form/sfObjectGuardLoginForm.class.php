<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardLoginForm login form.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage form
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardLoginForm extends PluginBasesfObjectGuardUserForm
{
  public function configure()
  {
    parent::configure();

    $this->validatorSchema->setPostValidator(new sfObjectGuardLoginValidator);
    $this->widgetSchema->setNameFormat('login[%s]');
  }
}