<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * Doctrine_Template_Guardable extends doctrine with Guardable behavior.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage behavior
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class Doctrine_Template_Guardable extends Doctrine_Template
{
  protected $_options = array(
    'className'     => '%CLASS%Guardable',
    'generateFiles' => false,
    'children'      => array()
  );

  public function __construct(array $options = array())
  {
    $this->_options = Doctrine_Lib::arrayDeepMerge($this->_options, $options);
    $this->_plugin = new Doctrine_Guardable($this->_options);
  }

  public function setTableDefinition()
  {
    $this->hasColumn('owner_id', 'integer', 4);
  }

  public function setUp()
  {
    $this->_plugin->initialize($this->_table);

    $this->hasOne('sfObjectGuardUser as Owner', array(
      'local'     => 'owner_id',
      'foreign'   => 'id',
      'onDelete'  => 'CASCADE'
    ));

    $this->hasMany('sfObjectGuardUser as Users', array(
      'local'         => 'object_id',
      'refClass'      => $this->_plugin->getTable()->getComponentName(),
      'foreign'       => 'user_id',
      'foreignAlias'  => 'Objects'
    ));

    $this->hasMany('sfObjectGuardGroup as Groups', array(
      'local'         => 'object_id',
      'refClass'      => $this->_plugin->getTable()->getComponentName(),
      'foreign'       => 'group_id',
      'foreignAlias'  => 'Objects'
    ));
  }

  /**
   * Returns query object to retrieve all user credentials
   *
   * @param sfObjectGuardUser $user user instance
   * @return Doctrine_Query
   */
  protected function getCredentialsForUserQuery(sfObjectGuardUser $user)
  {
    return Doctrine_Query::create()->
      select('g.id, o.id, p.name, o.owner_id')->
      from($this->_table->getComponentName() . ' o')->
      where('u.id = ? OR o.owner_id = ?', array($user->getId(), $user->getId()))->
      leftJoin('o.Users u')->
      leftJoin('o.Groups g')->
      leftJoin('g.Permissions p');
  }

  /**
   * Returns all credentials to current model for user
   *
   * @param   sfObjectGuardUser   $user   user instance
   * @return  array                       array of credentials
   */
  public function getCredentialsForUserTableProxy(sfObjectGuardUser $user)
  {
    $permissions = $this->getCredentialsForUserQuery($user)->
      execute(array(), Doctrine::HYDRATE_NONE);

    $credentials = array();
    foreach ($permissions as $permission)
    {
      if (!is_null($permission[3]))
      {
        $credentialString = $this->_table->
          getComponentName() . '/' . $permission[0] . '/' . $permission[3];
        if (!in_array($credentialString, $credentials))
        {
          $credentials[] = $credentialString;
        }
      }
      if ($permission[1] == $user->getId())
      {
        $credentialString = $this->_table->
          getComponentName() . '/' . $permission[0] . '/owner';
        if (!in_array($credentialString, $credentials))
        {
          $credentials[] = $credentialString;
        }
      }
    }

    return $credentials;
  }

  /**
   * Checks if user is owner of object
   *
   * @param   sfObjectGuardUser   $user   user instance
   * @return  boolean                     true if user is owner, false in other way
   */
  public function isOwner(sfObjectGuardUser $user)
  {
    return $user->getId() === $this->getInvoker()->getOwnerId();
  }

  /**
   * Add user to group of model
   *
   * @param sfObjectGuardUser   $user   user instance
   * @param sfObjectGuardGroup  $group  group instance
   * @param Doctrine_Connection $con
   * @return void
   */
  public function addUserToGroup(sfObjectGuardUser $user, sfObjectGuardGroup $group, $con = null)
  {
    $connection = $this->_plugin->getTable()->create();

    $connection->set('Object', $this->getInvoker());
    $connection->set('User', $user);
    $connection->set('Group', $group);
    $connection->save($con);
  }

  /**
   * Remove user from group of model
   *
   * @param sfObjectGuardUser   $user   user instance
   * @param sfObjectGuardGroup  $group  group instance
   * @return void
   */
  public function removeUserFromGroup(sfObjectGuardUser $user, sfObjectGuardGroup $group)
  {
    Doctrine_Query::create()->
      delete($this->_plugin->getTable()->getComponentName())->
      where('object_id = ?', $this->getInvoker()->getId())->
      andWhere('user_id = ?', $user->getId())->
      andWhere('group_id = ?', $group->getId())->
      execute();
  }

  /**
   * Checks if user is in group of model
   *
   * @param sfObjectGuardUser   $user   user instance
   * @param sfObjectGuardGroup  $group  user group
   * @return boolean true if user in group, false in other way
   */
  public function isUserInGroup(sfObjectGuardUser $user, sfObjectGuardGroup $group)
  {
    return Doctrine_Query::create()->
      from($this->_plugin->getTable()->getComponentName())->
      where('object_id = ?', $this->getInvoker()->getId())->
      andWhere('user_id = ?', $user->getId())->
      andWhere('group_id = ?', $group->getId())->
      execute()->
      count() > 0;
  }

  /**
   * Removes user from all groups in model
   *
   * @param sfObjectGuardUser $user user instance
   * @return void
   */
  public function removeUser(sfObjectGuardUser $user)
  {
    Doctrine_Query::create()->
      delete($this->_plugin->getTable()->getComponentName())->
      where('object_id = ?', $this->getInvoker()->getId())->
      andWhere('user_id = ?', $user->getId())->
      execute();
  }
}
