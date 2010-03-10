<?php

include dirname(__FILE__) . '/../bootstrap/doctrine.php';

$t = new lime_test(26, new lime_output_color);

$guardUser = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail('ever.zet@gmail.com');
$user = sfContext::getInstance()->getUser();

$t->comment('basic init tests');
$t->ok(!$user->isAuthenticated(), 'User is not authed by default');
$t->ok($user->isAnonymous(), 'User is anonymous by default');

$t->comment('not signed user model retrieve');
$t->is((string)$user, '', '__toString() returns empty string');
$t->ok(is_null($user->getEmail()), 'getEmail() returns null');
$t->ok(is_null($user->checkPassword('test_PaSS')), 'checkPassword() returns null');

$t->comment('signIn() tests');
$user->signIn($guardUser);
$t->ok($user->isAuthenticated(), 'User is now authed');
$t->ok(!$user->isAnonymous(), 'User is not anonymous anymore');

$t->comment('signed user model retrieve');
$t->is((string)$user, 'ever.zet@gmail.com', '__toString() works');
$t->is($user->getEmail(), 'ever.zet@gmail.com', 'getEmail() works');
$t->ok($user->checkPassword('test_PaSS'), 'checkPassword() works on right password');
$t->ok(!$user->checkPassword('test_PasS'), 'checkPassword() works on wrong password');
$t->is($guardUser, $user->getGuardUser(), 'getGuardUser() returns right model object');

$t->comment('credentials system check');
$sfObjectGuardTest = Doctrine::getTable('sfObjectGuardTest')->findOneByName('test');
$participantGroup = Doctrine::getTable('sfObjectGuardGroup')->findOneByName('participant');
$sfObjectGuardTest->addUserToGroup($guardUser, $participantGroup);
$queriesCount = $conn->count();
$t->ok(
  $user->hasCredential('sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/add_publication'),
  'User now have "right to add publication" into community'
);
$t->is($queriesCount, $conn->count() - 1, '"One query" fired to knew');
$t->ok($user->hasCredentialForObject('add_publication', $sfObjectGuardTest), 'hasCredentialForObject() returns true on "add_publication"');
$t->is($queriesCount, $conn->count() - 1, '"No query" fired to knew');
$t->ok(!$user->hasCredentialForObject('edit_publication', $sfObjectGuardTest), 'hasCredentialForObject() returns false on "edit_publication"');
$t->ok(
  !$user->hasCredential('sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/edit_publication'),
  'User "have no right to edit publication" in community'
);
$t->is($queriesCount, $conn->count() - 1, '"No query" fired to knew');
$t->ok($user->isCredentialsLoadedForTable(Doctrine::getTable('sfObjectGuardTest')), '"Credentials loaded" for community');
$sfObjectGuardTest->setOwner($guardUser);
$sfObjectGuardTest->save();
$t->ok(
  !$user->hasCredential('sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/edit_publication'),
  'User "still have no right to edit publication" in community'
);
$user->reloadCredentialsForTable(Doctrine::getTable('sfObjectGuardTest'));
$queriesCount = $conn->count();
$t->ok(
  $user->hasCredential('sfObjectGuardTest/' . $sfObjectGuardTest->getId() . '/edit_publication'),
  'User now "have right to edit publication" in community, because "he is owner"'
);
$t->is($queriesCount, $conn->count(), 'No queries runned after reloadCredentialsForTable()');
$t->ok($user->hasCredentialForObject('add_publication', $sfObjectGuardTest), 'hasCredentialForObject() also returns true');

$t->comment('signOut() tests');
$user->signOut();
$t->ok(!$user->isAuthenticated(), 'User is not authed anymore');
$t->ok($user->isAnonymous(), 'User is anonymous now');
