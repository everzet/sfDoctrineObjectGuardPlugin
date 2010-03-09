<?php

include dirname(__FILE__) . '/../bootstrap/doctrine.php';

$t = new lime_test(26, new lime_output_color);

$user1 = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail('ever.zet@gmail.com');
$user2 = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail('everzet@gmail.com');
$community = Doctrine::getTable('Community')->findOneByName('test');
$community2 = Doctrine::getTable('Community')->findOneByName('test2');
$participantGroup = Doctrine::getTable('sfObjectGuardGroup')->findOneByName('participant');
$moderatorGroup = Doctrine::getTable('sfObjectGuardGroup')->findOneByName('moderator');

$community->addUserToGroup($user1, $participantGroup);
$user1sInGroup = getUsersCountInGroup($user1, $participantGroup, $community);
$t->is($user1sInGroup, 1, 'User "added" as participant of community');

$community->removeUserFromGroup($user1, $participantGroup);
$user1sInGroup = getUsersCountInGroup($user1, $participantGroup, $community);
$t->is($user1sInGroup, 0, 'User "removed" from participants of community');

$t->comment('Multigroup testing');
$community->addUserToGroup($user1, $participantGroup);
$community->addUserToGroup($user1, $moderatorGroup);
$user1sInGroup = getUsersCountInGroup($user1, $moderatorGroup, $community);
$t->is($user1sInGroup, 1, 'User is "moderator"');

$user1sInGroup = getUsersCountInGroup($user1, $participantGroup, $community);
$t->is($user1sInGroup, 1, 'User is "participant"');

$t->comment('Is in group');
$t->is($community->isUserInGroup($user1, $moderatorGroup), true, 'User "is moderator"');

$community->removeUserFromGroup($user1, $moderatorGroup);
$t->is($community->isUserInGroup($user1, $moderatorGroup), false, '"is not moderator"');

$t->comment('Communities retrieve');
$community->addUserToGroup($user1, $moderatorGroup);
$community->addUserToGroup($user2, $participantGroup);
$communities = Doctrine::getTable('Community')->getAll();

$t->is(count($communities), 3, 'Communities "count is 3"');
$t->is(count($communities[0]['Users']), 2, 'First community "userCount is 2"');
$t->is($communities[0]['name'], 'test', 'First community "name is test"');
$t->is(count($communities[1]['Users']), 0, 'Second community "userCount is 0"');
$t->is($communities[1]['description'], '2222', 'Second community "description is 2222"');

$t->comment('User credentials');
$t->is_deeply(Doctrine::getTable('Community')->getCredentialsForUser($user1), array(
  'community/' . $community->getId() . '/add_publication',
  'community/' . $community->getId() . '/dismiss_user',
  'community/' . $community->getId() . '/fry_publication',
  'community/' . $community->getId() . '/promote_moderator'
), 'All "credentials for moderator" loaded properly');

$community->removeUserFromGroup($user1, $moderatorGroup);
$t->is_deeply(Doctrine::getTable('Community')->getCredentialsForUser($user1), array(
  'community/' . $community->getId() . '/add_publication'
), 'All "credentials for participant" loaded properly');

$community->removeUser($user2);
$t->is(Doctrine::getTable('Community')->getCredentialsForUser($user2), array(
), 'All "credentials" "for no one" loaded properly');

$t->comment('Set/Get user as owner');
$community->setOwner($user1);
$community->save();
$t->is_deeply(Doctrine::getTable('Community')->getCredentialsForUser($user1), array(
  'community/' . $community->getId() . '/add_publication',
  'community/' . $community->getId() . '/owner'
), 'All "credentials for owner/participant" loaded properly');

$community2->setOwner($user1);
$community2->save();
$t->is_deeply(Doctrine::getTable('Community')->getCredentialsForUser($user1), array(
  'community/' . $community->getId() . '/add_publication',
  'community/' . $community->getId() . '/owner',
  'community/' . $community2->getId() . '/owner'
), 'All "credentials for owner/owner/participant" with bouth groups loaded properly');

$community2->addUserToGroup($user1, $participantGroup);
$t->is_deeply(Doctrine::getTable('Community')->getCredentialsForUser($user1), array(
  'community/' . $community->getId() . '/add_publication',
  'community/' . $community->getId() . '/owner',
  'community/' . $community2->getId() . '/add_publication',
  'community/' . $community2->getId() . '/owner'
), 'All "credentials for owner/owner/participant/participant" with bouth groups loaded properly');

$community2->addUserToGroup($user1, $moderatorGroup);
$t->is_deeply(Doctrine::getTable('Community')->getCredentialsForUser($user1), array(
  'community/' . $community->getId() . '/add_publication',
  'community/' . $community->getId() . '/owner',
  'community/' . $community2->getId() . '/add_publication',
  'community/' . $community2->getId() . '/owner',
  'community/' . $community2->getId() . '/dismiss_user',
  'community/' . $community2->getId() . '/fry_publication',
  'community/' . $community2->getId() . '/promote_moderator'
), 'All "credentials for owner/owner/participant/participant/moderator" with bouth groups loaded properly');

$community2->setOwnerId(null);
$community2->save();
$t->is_deeply(Doctrine::getTable('Community')->getCredentialsForUser($user1), array(
  'community/' . $community2->getId() . '/add_publication',
  'community/' . $community2->getId() . '/dismiss_user',
  'community/' . $community2->getId() . '/fry_publication',
  'community/' . $community2->getId() . '/promote_moderator',
  'community/' . $community->getId() . '/add_publication',
  'community/' . $community->getId() . '/owner'
), 'All "credentials for owner/participant/participant/moderator" with bouth groups loaded properly');

$t->is($community->getOwnerId(), $user1->getId(), '"getOwnerId" method works');
$t->is($community->getOwner(), $user1, '"getOwner" method works');
$t->is($community->isOwner($user1), true, '"isOwner" method works "on pure object"');
$t->is($community->isOwner($user2), false, '"isOwner" method "returns false" on wrong user');

$t->comment('Remove user from all groups');
$t->is($community->isUserInGroup($user1, $participantGroup), true, 'User "is participant"');
$community->removeUser($user1);
$t->is($community->isUserInGroup($user1, $participantGroup), false, 'User "is not participant"');
$t->is($community->isUserInGroup($user1, $participantGroup), false, 'User "is not moderator"');

function getUsersCountInGroup(sfObjectGuardUser $user1, sfObjectGuardGroup $group, Community $com)
{
  return Doctrine_Query::create()->
    from('CommunityGuardable')->
    where('object_id = ?', $com->getId())->
    andWhere('user_id = ?', $user1->getId())->
    andWhere('group_id = ?', $group->getId())->
    execute()->
    count();
}
