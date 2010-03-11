<?php

include(dirname(__FILE__).'/../../bootstrap/doctrine.php');

$browser = new sfTestFunctional(new sfBrowser());

$browser->
  info('1. Active user autrhorization test')->
  get('/')->

  with('user')->
    isAuthenticated(false)->

  info('  1.1 Login with activated & wrong pass account')->
  get('/login')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'login')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'Enter to site')->
    checkForm('sfObjectGuardLoginForm')->
    checkElement('form input[type=submit]', 1)->
  end()->

  info('  1.2 Submitted login form with wrong pass')->
  click('form input[type=submit]', array('login' => array(
    'email'     => 'ever.zet@gmail.com',
    'password'  => 'test_PaasSS'
  )))->

  with('form')->begin()->
    hasErrors(true)->
  end()->

  with('user')->
    isAuthenticated(false)->

  info('  1.3 Login with activated account')->
  get('/login')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'login')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'Enter to site')->
    checkForm('sfObjectGuardLoginForm')->
    checkElement('form input[type=submit]', 1)->
  end()->

  info('  1.4 Submitted login form')->
  click('form input[type=submit]', array('login' => array(
    'email'     => 'ever.zet@gmail.com',
    'password'  => 'test_PaSS'
  )))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  with('response')->
    isRedirected()->
    followRedirect()->

  with('user')->
    isAuthenticated(true)->

  info('2. Active user password change')->
  get('/user/password')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'password')->
  end()->  

  with('response')->begin()->
    isStatusCode(200)->
    checkForm('sfObjectGuardPasswordForm')->
    checkElement('form input[type=submit]', 1)->
  end()->

  info('  2.2 Submitted password form')->
  click('form input[type=submit]', array('password' => array(
    'password'  => ($newPass = 'n3w_PASsS')
  )))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  with('response')->
    isRedirected()->
    followRedirect()->

  info('  2.3 Logout user')->
  get('/logout')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'logout')->
  end()->

  with('response')->
    isRedirected()->
    followRedirect()->

  with('user')->
    isAuthenticated(false)->

  info('  2.4 Login with changed password')->
  get('/login')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'login')->
  end()->

  click('form input[type=submit]', array('login' => array(
    'email'     => 'ever.zet@gmail.com',
    'password'  => $newPass
  )))->

  with('form')->begin()->
    hasErrors(false)->
  end()->

  with('response')->
    isRedirected()->
    followRedirect()->

  with('user')->
    isAuthenticated(true)->

  info('3. Non-active user autrhorization test')->
  get('/')->
  
  info('  3.1 Logout user')->
  get('/logout')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'logout')->
  end()->

  with('response')->
    isRedirected()->
    followRedirect()->

  with('user')->
    isAuthenticated(false)->

  info('  3.2 Login with non-active account')->
  get('/login')->
  with('request')->begin()->
    isParameter('module', 'sfObjectGuardAuth')->
    isParameter('action', 'login')->
  end()->

  with('response')->begin()->
    isStatusCode(200)->
    checkElement('h1', 'Enter to site')->
    checkForm('sfObjectGuardLoginForm')->
    checkElement('form input[type=submit]', 1)->
  end()->

  info('  3.3 Submitted login form')->
  click('form input[type=submit]', array('login' => array(
    'email'     => 'everzet@gmail.com',
    'password'  => 'tesPasas123sa'
  )))->

  with('form')->begin()->
    hasErrors(true)->
  end()->

  with('user')->
    isAuthenticated(false)
;
