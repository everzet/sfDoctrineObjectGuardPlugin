<?php

include dirname(__FILE__) . '/../bootstrap/doctrine.php';

$t = new lime_test(20, new lime_output_color);

$user = new sfObjectGuardUser;
$user->setEmail('test@test.ru');
$user->save();
$uId = $user->getId();
$t->isnt($uId, null, 'User "is saved"');

$activationKey = new sfObjectGuardActivationKey;
$activationKey->setKeyType(
  Doctrine::getTable('sfObjectGuardActivationKeyType')->findOneByName('register')
);
$activationKey->setUser($user);
$activationKey->save();

$t->is($user->getActivationKeys()->getKeyType()->getName(), 'register', 'Activation key type of the user is "register"');

$t->is($activationKey->getUserId(), $user->getId(), 'Activation key "connected with user"');

$user->setPassword('testerPaSss');
$user->save();
$user = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail('test@test.ru');
$t->is($user->getId(), $uId, 'User "retrieved"');

$t->ok($user->checkPassword('testerPaSss'), 'Password "is right"');
$t->ok(!$user->checkPassword('testerPasss'), 'Password with "wrong case" is not right');
$t->ok(!$user->checkPassword('ABC'), 'Password is "totally wrong"');
$t->ok(!$user->getIsActive(), 'New user is "not active"');

$user->setActiveWithKey($activationKey);
$user->save();
$activationKey->delete();
$t->ok($user->getIsActive(), 'User is now "active"');
$t->is($user->getKeyType()->getName(), 'register', 'User activated with "keyType = register"');
$t->is($user->getInviterId(), null, 'User does not have inviter');

$user2 = Doctrine::getTable('sfObjectGuardUser')->findOneByEmail('everzet@gmail.com');

$t->is($user2->getEmail(), 'everzet@gmail.com', 'User 2 "email is everzet@gmail.com"');
$t->ok(!$user2->getIsActive(), 'User 2 is "not active"');

$activationKey = new sfObjectGuardActivationKey;
$activationKey->setKeyType(
  Doctrine::getTable('sfObjectGuardActivationKeyType')->findOneByName('invite')
);
$activationKey->setUser($user2);
$activationKey->setInviter($user);
$activationKey->save();

$t->is($activationKey->getInviterId(), $user->getId(), 'New activation key "with inviter" added');
$t->is($activationKey->getKeyType()->getName(), 'invite', 'New activation key type is "invite"');

$t->is(
  Doctrine_Query::create()->from('sfObjectGuardActivationKey')->execute()->count(),
  1, 'Activation "keys count is 1"'
);

$user2->setActiveWithKey($activationKey);
$user2->save();
$activationKey->delete();

$t->ok($user2->getIsActive(), 'User 2 is now "active"');
$t->is($user2->getKeyType()->getName(), 'invite', 'User 2 activated with "keyType = invite"');
$t->is($user2->getInviter()->getId(), $user->getId(), 'User 2 "inviter is User"');

$t->is(
  Doctrine_Query::create()->from('sfObjectGuardActivationKey')->execute()->count(),
  0, 'Activation "keys count is 0"'
);
