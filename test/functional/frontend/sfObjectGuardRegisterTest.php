<?php

include(dirname(__FILE__).'/../../bootstrap/doctrine.php');

$browser = new sfTestFunctional(new sfBrowser());
$browser->setTester('mailerx', 'sfTesterMailerExt');

$browser->
  info('1. Register user')->

  info('  1.1 Register page')->
  get('/register')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardRegister')->
    isParameter('action', 'index')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'Registration')->
    checkForm('sfObjectGuardRegisterForm')->
    checkElement('form input[type=submit]', 1)->
  end()->

  info('  1.2 Submitted form with existent email')->
  click('form input[type=submit]', array('register' => array(
    'email' => ($email = 'ever.zet@gmail.com')
  )))->

  with('form')->begin()->
    hasErrors(true)->
  end()->

  info('  1.3 Submitted form with new email')->
  click('form input[type=submit]', array('register' => array(
    'email' => ($email = 'tester@super.com')
  )))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  info('  1.4 Mail sending')->
  with('mailerx')->begin()->
    hasSent(1)->
    withMessage('tester@super.com')->
      checkHeader('Subject', '/Account activation for/')->
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
    checkElement('p', '/We have successfully sent activation key to your email/')->
  end()->

  info('2. Account activation')->
  get($activationMailLink[1])->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'activate')->
  end()->

  with('response')->
    isRedirected()->
    followRedirect()->

  info('3. Check user')->
  get('/logout')->
  with('response')->
    isRedirected()->
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
    isRedirected()->
    followRedirect()->

  with('user')->
    isAuthenticated(true)
;
