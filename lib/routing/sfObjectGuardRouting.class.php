<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfObjectGuardRouting routes configurator.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage routing
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfObjectGuardRouting
{
  /**
   * Listens to the routing.load_configuration event & adds auth pages routes if needed
   *
   * @param sfEvent An sfEvent instance
   */
  static public function addAuthRoutes(sfEvent $event)
  {
    $routes = $event->getSubject();

    $routes->prependRoute(
      'sf_object_guard_login', 
      new sfRoute('/login', array('module' => 'sfObjectGuardAuth', 'action' => 'login'))
    );
    $routes->prependRoute(
      'sf_object_guard_logout', 
      new sfRoute('/logout', array('module' => 'sfObjectGuardAuth', 'action' => 'logout'))
    );
    $routes->prependRoute(
      'sf_object_guard_activate',
      new sfDoctrineRoute('/users/activate/:activation_key', array(
        'module'  => 'sfObjectGuardAuth',
        'action'  => 'activate'
      ), array(), array(
        'model'   => 'sfObjectGuardActivationKey',
        'type'    => 'object',
      ))
    );
    $routes->prependRoute(
      'sf_object_guard_password', 
      new sfRoute('/users/password', array('module' => 'sfObjectGuardAuth', 'action' => 'password'))
    );
  }

  /**
   * Listens to the routing.load_configuration event & adds invite page routes if needed
   *
   * @param sfEvent An sfEvent instance
   */
  static public function addInviteRoutes(sfEvent $event)
  {
    $event->getSubject()->prependRoute(
      'sf_object_guard_invite', 
      new sfRoute('/users/invite', array('module' => 'sfObjectGuardInvite', 'action' => 'index'))
    );
  }

  /**
   * Listens to the routing.load_configuration event & adds register page routes if needed
   *
   * @param sfEvent An sfEvent instance
   */
  static public function addRegisterRoutes(sfEvent $event)
  {
    $event->getSubject()->prependRoute(
      'sf_object_guard_register', 
      new sfRoute('/register', array('module' => 'sfObjectGuardRegister', 'action' => 'index'))
    );
  }

  /**
   * Listens to the routing.load_configuration event & adds password remind page routes if needed
   *
   * @param sfEvent An sfEvent instance
   */
  static public function addPasswordRemindRoutes(sfEvent $event)
  {
    $event->getSubject()->prependRoute(
      'sf_object_guard_password_remind', 
      new sfRoute('/users/remind', array('module' => 'sfObjectGuardPasswordRemind', 'action' => 'index'))
    );
  }

  /**
   * Listens to the routing.load_configuration event & adds password remind page routes if needed
   *
   * @param sfEvent An sfEvent instance
   */
  static public function addCredentialsTestRoutes(sfEvent $event)
  {
    $routes = $event->getSubject();

    //$routes->prependRoute(
    //  'sf_object_guard_test_add_publication', 
    //  new sfRoute('/sf_object_guard_test/add_publication', array(
    //    'module' => 'sfObjectGuardCredentialsTest', 'action' => 'addPublication'
    //  ))
    //);

    $routes->prependRoute(
      'sf_object_guard_test_add_publication',
      new sfDoctrineRoute('/sf_object_guard_test/:id/add_publication', array(
        'module'  => 'sfObjectGuardCredentialsTest',
        'action'  => 'addPublication'
      ), array(), array(
        'model'   => 'sfObjectGuardTest',
        'type'    => 'object',
      ))
    );

    $routes->prependRoute(
      'sf_object_guard_test_edit_publication',
      new sfDoctrineRoute('/sf_object_guard_test/:id/edit', array(
        'module'  => 'sfObjectGuardCredentialsTest',
        'action'  => 'editPublication'
      ), array(), array(
        'model'   => 'sfObjectGuardTest',
        'type'    => 'object',
      ))
    );
  }
}
