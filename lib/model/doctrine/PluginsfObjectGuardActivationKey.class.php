<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginsfObjectGuardActivationKey extends BasesfObjectGuardActivationKey
{
  public function preSave($event)
  {
    $this->setActivationKey(md5(time()));
  }
}