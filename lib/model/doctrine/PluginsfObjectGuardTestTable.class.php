<?php
/**
 */
class PluginsfObjectGuardTestTable extends Doctrine_Table
{
  public function getAll()
  {
    return $this->createQuery('ogt')
      ->execute();
  }
}