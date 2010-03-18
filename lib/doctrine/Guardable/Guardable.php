<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Doctrine_Guardable introduces Guardable behavior.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage behavior
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class Doctrine_Guardable extends Doctrine_Record_Generator
{
  protected $_options = array();

  public function __construct(array $options = array())
  {
    $this->_options = $options;
  }

  public function setTableDefinition()
  {
    $this->hasColumn('id',        'integer', 4, array('primary' => true, 'autoincrement' => true));
    $this->hasColumn('object_id', 'integer', 4, array('primary' => true));
    $this->hasColumn('user_id',   'integer', 4, array('primary' => true));
    $this->hasColumn('group_id',  'integer', 4, array('primary' => true));
  }

  public function buildRelation()
  {
    $this->_table->bind(array('sfObjectGuardUser as User', array(
      'local'     => 'user_id',
      'foreign'   => 'id',
      'onDelete'  => 'CASCADE'
    )), Doctrine_Relation::ONE);

    $this->_table->bind(array('sfObjectGuardGroup as Group', array(
      'local'     => 'group_id',
      'foreign'   => 'id',
      'onDelete'  => 'CASCADE'
    )), Doctrine_Relation::ONE);

    $this->_table->bind(array($this->getOption('table')->getComponentName() . ' as Object', array(
      'local'     => 'object_id',
      'foreign'   => 'id',
      'onDelete'  => 'CASCADE'
    )), Doctrine_Relation::ONE);

    $this->actAs(new Doctrine_Template_Timestampable);
  }
}