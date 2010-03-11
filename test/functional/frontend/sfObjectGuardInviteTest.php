<?php

include(dirname(__FILE__).'/../../bootstrap/doctrine.php');

$browser = new sfTestFunctional(new sfBrowser());
$browser->setTester('mailerx', 'sfTesterMailerExt');

$browser->
  info('1. Invite user')->

  info('  1.1 Invite page')->
  get('/user/invite')->
  with('response')->isStatusCode(302)->

  get('/login')->

  click('form input[type=submit]', array('login' => array(
    'email'     => 'ever.zet@gmail.com',
    'password'  => 'test_PaSS'
  )))->

  with('response')->
    followRedirect()->

  with('user')->
    isAuthenticated(true)->

  get('/user/invite')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardInvite')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'Invite user')->
    checkForm('sfObjectGuardInviteForm')->
    checkElement('form input[type=submit]', 1)->
  end()->

  info('  1.2 Submitted form with existent email')->
  click('form input[type=submit]', array('invite' => array(
    'email' => 'ever.zet@gmail.com'
  )))->

  with('form')->begin()->
    hasErrors(true)->
  end()->

  info('  1.3 Submitted form with new email')->
  click('form input[type=submit]', array('invite' => array(
    'email' => ($email = 'tester@super.com')
  )))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  info('  1.4 Mail sending')->
  with('mailerx')->begin()->
    hasSent(1)->
    withMessage('tester@super.com')->
      checkHeader('Subject', '/Invite to the/')->
      checkBody('/Go to the following link to finish registration:/')->
      checkBody('/<a href=\".*\/user\/activate\/.*\">/')->
      regexpBody('/<a href=\".*(\/user\/activate\/[^\"\/]*)\">/', $activationMailLink)->
      checkBody(sprintf('/Your email: \"%s\"/', $email))->
      checkBody('/Your temporary password: \"[^\"]*\"/')->
      regexpBody('/Your temporary password: \"([^\"]*)\"/', $activationMailPassword)->
  end()->

  with('response')->
    isRedirected()->
    followRedirect()->

  with('response')->begin()->
    checkElement('p', '/We have successfully sent registration instructions to the specified email/')->
  end()->

  info('2. Account activation')->
  get($activationMailLink[1])->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'activate')->
  end()->

  with('response')->
    followRedirect()->

  info('3. Check user')->
  get('/logout')->
  with('response')->
    followRedirect()->

  get('/login')->

  click('form input[type=submit]', array('login' => array(
    'email'     => $email,
    'password'  => $activationMailPassword[1]
  )))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  with('response')->
    followRedirect()->

  with('user')->
    isAuthenticated(true)
;
