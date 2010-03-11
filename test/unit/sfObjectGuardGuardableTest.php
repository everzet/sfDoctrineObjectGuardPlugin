<?php

include dirname(__FILE__) . '/../bootstrap/doctrine.php';

$t = new lime_test(26, new lime_output_color);

$user1 = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail('ever.zet@gmail.com');
$user2 = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail('everzet@gmail.com');
$sfObjectGuardTest = Doctrine::getTable('sfObjectGuardTest')->findOneByName('test');
$sfObjectGuardTest2 = Doctrine::getTable('sfObjectGuardTest')->findOneByName('test2');
$participantGroup = Doctrine::getTable('sfObjectGuardGroup')->findOneByName('participant');
$moderatorGroup = Doctrine::getTable('sfObjectGuardGroup')->findOneByName('moderator');

$sfObjectGuardTest->addUserToGroup($user1, $participantGroup);
$user1sInGroup = getUsersCountInGroup($user1, $participantGroup, $sfObjectGuardTest);
$t->is($user1sInGroup, 1, 'User "added" as participant of community');

$sfObjectGuardTest->removeUserFromGroup($user1, $participantGroup);
$user1sInGroup = getUsersCountInGroup($user1, $participantGroup, $sfObjectGuardTest);
$t->is($user1sInGroup, 0, 'User "removed" from participants of community');

$t->comment('Multigroup testing');
$sfObjectGuardTest->addUserToGroup($user1, $participantGroup);
$sfObjectGuardTest->addUserToGroup($user1, $moderatorGroup);
$user1sInGroup = getUsersCountInGroup($user1, $moderatorGroup, $sfObjectGuardTest);
$t->is($user1sInGroup, 1, 'User is "moderator"');

$user1sInGroup = getUsersCountInGroup($user1, $participantGroup, $sfObjectGuardTest);
$t->is($user1sInGroup, 1, 'User is "participant"');

$t->comment('Is in group');
$t->is($sfObjectGuardTest->isUserInGroup($user1, $moderatorGroup), true, 'User "is moderator"');

$sfObjectGuardTest->removeUserFromGroup($user1, $moderatorGroup);
$t->is($sfObjectGuardTest->isUserInGroup($user1, $moderatorGroup), false, '"is not moderator"');

$t->comment('Communities retrieve');
$sfObjectGuardTest->addUserToGroup($user1, $moderatorGroup);
$sfObjectGuardTest->addUserToGroup($user2, $participantGroup);
$communities = Doctrine::getTable('sfObjectGuardTest')->getAll();

$t->is(count($communities), 3, 'Communities "count is 3"');
$t->is(count($communities[0]['Users']), 2, 'First community "userCount is 2"');
$t->is($communities[0]['name'], 'test', 'First community "name is test"');
$t->is(count($communities[1]['Users']), 0, 'Second community "userCount is 0"');
$t->is($communities[1]['description'], '2222', 'Second community "description is 2222"');

$t->comment('User credentials');
$t->is_deeply(Doctrine::getTable('sfObjectGuardTest')->getCredentialsForUser($user1), array(
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/dismiss_user',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/fry_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/promote_moderator'
), 'All "credentials for moderator" loaded properly');

$sfObjectGuardTest->removeUserFromGroup($user1, $moderatorGroup);
$t->is_deeply(Doctrine::getTable('sfObjectGuardTest')->getCredentialsForUser($user1), array(
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/add_publication'
), 'All "credentials for participant" loaded properly');

$sfObjectGuardTest->removeUser($user2);
$t->is(Doctrine::getTable('sfObjectGuardTest')->getCredentialsForUser($user2), array(
), 'All "credentials" "for no one" loaded properly');

$t->comment('Set/Get user as owner');
$sfObjectGuardTest->setOwner($user1);
$sfObjectGuardTest->save();
$t->is_deeply(Doctrine::getTable('sfObjectGuardTest')->getCredentialsForUser($user1), array(
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/owner'
), 'All "credentials for owner/participant" loaded properly');

$sfObjectGuardTest2->setOwner($user1);
$sfObjectGuardTest2->save();
$t->is_deeply(Doctrine::getTable('sfObjectGuardTest')->getCredentialsForUser($user1), array(
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/owner',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/owner'
), 'All "credentials for owner/owner/participant" with both groups loaded properly');

$sfObjectGuardTest2->addUserToGroup($user1, $participantGroup);
$t->is_deeply(Doctrine::getTable('sfObjectGuardTest')->getCredentialsForUser($user1), array(
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/owner',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/owner'
), 'All "credentials for owner/owner/participant/participant" with both groups loaded properly');

$sfObjectGuardTest2->addUserToGroup($user1, $moderatorGroup);
$t->is_deeply(Doctrine::getTable('sfObjectGuardTest')->getCredentialsForUser($user1), array(
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/owner',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/owner',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/dismiss_user',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/fry_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/promote_moderator'
), 'All "credentials for owner/owner/participant/participant/moderator" with both groups loaded properly');

$sfObjectGuardTest2->setOwnerId(null);
$sfObjectGuardTest2->save();
$t->is_deeply(Doctrine::getTable('sfObjectGuardTest')->getCredentialsForUser($user1), array(
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/dismiss_user',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/fry_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest2->getId() . '/promote_moderator',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/add_publication',
  'sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/owner'
), 'All "credentials for owner/participant/participant/moderator" with both groups loaded properly');

$t->is($sfObjectGuardTest->getOwnerId(), $user1->getId(), '"getOwnerId" method works');
$t->is($sfObjectGuardTest->getOwner(), $user1, '"getOwner" method works');
$t->is($sfObjectGuardTest->isOwner($user1), true, '"isOwner" method works "on pure object"');
$t->is($sfObjectGuardTest->isOwner($user2), false, '"isOwner" method "returns false" on wrong user');

$t->comment('Remove user from all groups');
$t->is($sfObjectGuardTest->isUserInGroup($user1, $participantGroup), true, 'User "is participant"');
$sfObjectGuardTest->removeUser($user1);
$t->is($sfObjectGuardTest->isUserInGroup($user1, $participantGroup), false, 'User "is not participant"');
$t->is($sfObjectGuardTest->isUserInGroup($user1, $participantGroup), false, 'User "is not moderator"');

function getUsersCountInGroup(sfObjectGuardUser $user1, sfObjectGuardGroup $group, sfObjectGuardTest $com)
{
  return Doctrine_Query::create()->
    from('sfObjectGuardTestGuardable')->
    where('object_id = ?', $com->getId())->
    andWhere('user_id = ?', $user1->getId())->
    andWhere('group_id = ?', $group->getId())->
    execute()->
    count();
}
