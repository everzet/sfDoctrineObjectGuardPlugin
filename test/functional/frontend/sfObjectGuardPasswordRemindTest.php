<?php

include(dirname(__FILE__).'/../../bootstrap/doctrine.php');

$browser = new sfTestFunctional(new sfBrowser());
$browser->setTester('mailerx', 'sfTesterMailerExt');

$browser->
  info('1. Password reminder test')->
  get('/')->

  info('  1.1 Login with activated account')->
  get('/login')->

  click('form input[type=submit]', array('login' => array(
    'email'     => 'ever.zet@gmail.com',
    'password'  => 'test_PaSS'
  )))->

  with('response')->
    followRedirect()->

  with('user')->
    isAuthenticated(true)->

  get('/logout')->
  with('response')->
    followRedirect()->

  info('  1.2 Password reminder')->
  get('/user/remind')->

  with('request')->begin()->
    isParameter('module', 'sfObjectGuardPasswordRemind')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'Password recovery')->
    checkForm('sfObjectGuardReminderForm')->
    checkElement('form input[type=submit]', 1)->
  end()->

  info('  1.3 Submitted form with email')->
  click('form input[type=submit]', array('reminder' => array(
    'email' => ($email = 'ever.zet@gmail.com')
  )))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  info('  1.4 Mail sending')->
  with('mailerx')->begin()->
    hasSent(1)->
    withMessage($email)->
      checkHeader('Subject', '/Password recovery for/')->
      checkBody('/Go to the following link to proceed with password change:/')->
      checkBody('/<a href=\".*\/user\/activate\/.*\">/')->
      regexpBody('/<a href=\".*(\/user\/activate\/[^\"\/]*)\">/', $activationMailLink)->
      checkBody(sprintf('/Your email: \"%s\"/', $email))->
      checkBody('/Your new password: \"[^\"]*\"/')->
      regexpBody('/Your new password: \"([^\"]*)\"/', $activationMailPassword)->
  end()->

  with('response')->
    isRedirected()->
    followRedirect()->

  with('response')->begin()->
    checkElement('p', '/We have successfully sent password recovery instruction to your email/')->
  end()->

  info('  1.4 Activate new password')->
  get($activationMailLink[1])->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'activate')->
  end()->

  with('response')->
    followRedirect()->

  get('/logout')->
  with('response')->
    followRedirect()->

  get('/login')->

  click('form input[type=submit]', array('login' => array(
    'email'     => $email,
    'password'  => $activationMailPassword[1]
  )))->

  with('response')->
    followRedirect()->

  with('user')->
    isAuthenticated(true)
;
