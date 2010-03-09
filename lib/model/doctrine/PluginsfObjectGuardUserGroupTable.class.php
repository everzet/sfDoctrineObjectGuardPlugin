<?php
/**
 * This class has been auto-generated by the Doctrine ORM Framework
 */
class PluginsfObjectGuardUserGroupTable extends Doctrine_Table
{
  public function getCredentialsForUserQuery($user)
  {
    return Doctrine_Query::create()->
      select('p.name as perm_name')->
      from('sfObjectGuardUserGroup ug')->
      leftJoin('ug.sfObjectGuardGroup g')->
      leftJoin('g.permissions p')->
      where('ug.user_id = ?', $user->getId());
  }

  public function getCredentialsForUser($user)
  {
    $permissions = $this->
      getCredentialsForUserQuery($user)->
      fetchArray();

    $credentials = array();
    foreach ($permissions as $permission)
    {
      $credentials[] = 'global/' . $permission['perm_name'];
    }

    return $credentials;
  }
}
