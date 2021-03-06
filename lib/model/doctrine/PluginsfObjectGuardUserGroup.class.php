<?php

/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
abstract class PluginsfObjectGuardUserGroup extends BasesfObjectGuardUserGroup
{
  public function addUserToGroup($user, $group, $con = null)
  {
    $connection = new sfObjectGuardUserGroup;

    $connection->set('sfObjectGuardUser', $user);
    $connection->set('sfObjectGuardGroup', $group);
    $connection->save($con);
  }

  public function removeUserFromGroup($user, $group)
  {
    Doctrine_Query::create()->
      delete('sfObjectGuardUserGroup')->
      andWhere('user_id = ?', $user->getId())->
      andWhere('group_id = ?', $group->getId())->
      execute();
  }

  public function isUserInGroup($user, $group)
  {
    return Doctrine_Query::create()->
      from('sfObjectGuardUserGroup')->
      andWhere('user_id = ?', $user->getId())->
      andWhere('group_id = ?', $group->getId())->
      execute()->
      count() > 0;
  }
}
