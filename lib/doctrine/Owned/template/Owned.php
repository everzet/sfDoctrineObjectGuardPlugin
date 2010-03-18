<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Doctrine_Template_Owned extends doctrine with Owned behavior.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage behavior
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class Doctrine_Template_Owned extends Doctrine_Template
{
  protected $_options = array('ownerColumn' => 'owner_id');

  public function setTableDefinition()
  {
    $this->hasColumn($this->_options['ownerColumn'], 'integer', 4);
  }

  public function setUp()
  {
    $this->hasOne('sfObjectGuardUser as Owner', array(
      'local'     => $this->_options['ownerColumn'],
      'foreign'   => 'id',
      'onDelete'  => 'CASCADE'
    ));
  }
}
