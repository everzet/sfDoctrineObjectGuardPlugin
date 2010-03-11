<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardRegisterForm register form.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage form
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardRegisterForm extends PluginBasesfObjectGuardUserForm
{
  protected $password;

  public function configure()
  {
    parent::configure();
    unset($this['password']);

    $this->validatorSchema->setPostValidator(new sfObjectGuardRegisterValidator);
    $this->widgetSchema->setNameFormat('register[%s]');
  }

  public function setPassword($password)
  {
    $this->password = $password;
  }

  public function doSave($con = null)
  {
    $this->getObject()->setPassword($this->password);

    parent::doSave($con);
  }
}
