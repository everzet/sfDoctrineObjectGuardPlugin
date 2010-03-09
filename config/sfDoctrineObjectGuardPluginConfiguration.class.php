<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * sfDoctrineObjectGuardPluginConfiguration configuration.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage config
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class sfDoctrineObjectGuardPluginConfiguration extends sfPluginConfiguration
{
  /**
   * @see sfPluginConfiguration
   */
  public function initialize()
  {
    if (sfConfig::get('app_sf_object_guard_plugin_routes_register', true))
    {
      $modules = array(
        'sfObjectGuardAuth',
        'sfObjectGuardInvite',
        'sfObjectGuardRegister',
        'sfObjectGuardPasswordRemind'
      );

      foreach ($modules as $module)
      {
        if (in_array($module, sfConfig::get('sf_enabled_modules', array())))
        {
          $this->dispatcher->connect('routing.load_configuration', array('sfObjectGuardRouting',
            'add' . str_replace('sfObjectGuard', '', $module) . 'Routes'));
        }
      }
    }

    $this->dispatcher->connect('controller.change_action',
      array('sfObjectGuardPasswordChecker', 'checkUserPassword'));
  }
}