<?php

/*
 * This file is part of the sfDoctrineObjectGuardPlugin.
 * (c) 2009 Konstantin Kudryashov <ever.zet@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

/**
 * BasesfObjectGuardAuthActions implements auth actions.
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage actions
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 * @version    1.0.0
 */
class BasesfObjectGuardAuthActions extends sfActions
{
  /**
   * Executes login action
   *
   * @param sfRequest $request A request object
   */
  public function executeLogin(sfWebRequest $request)
  {
    $this->form = $this->getLoginForm();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $values   = $this->form->getValues();
        $remember = isset($values['remember']) ? $values['remember'] : false;
       	$this->getUser()->signin($values['user'], $remember);

        return $this->redirect('@homepage');
      }
    }
  }

  /**
   * Returns new login form
   *
   * @return sfForm
   */
  protected function getLoginForm()
  {
    return new sfObjectGuardLoginForm;
  }

  /**
   * Executes logout action
   *
   * @param sfRequest $request A request object
   */
  public function executeLogout(sfWebRequest $request)
  {
    $this->getUser()->signout();

    return $this->redirect('@homepage');
  }

  /**
   * Executes Activate action
   *
   * @param sfRequest $request A request object
   */
  public function executeActivate(sfWebRequest $request)
  {
    $activationKey = $this->getRoute()->getObject();
    $user = $activationKey->getUser();

    if ('password' == $activationKey->getKeyType()->getName())
    {
      $user->setPassword($activationKey->getAdditional());
      $this->getUser()->signIn($user);
      $activationKey->delete();
    }
    else
    {
      $user->setActiveWithKey($activationKey);
      $user->save();
      $this->getUser()->signIn($user);
      $activationKey->delete();
    }

    return $this->redirect('@homepage');
  }

  /**
   * Executes Password action for changing password
   *
   * @param sfRequest $request A request object
   */
  public function executePassword(sfWebRequest $request)
  {
    $user = $this->getUser()->getGuardUser();
    $this->redirectUnless($user, '@homepage');

    $this->form = $this->getPasswordForm();

    if ($request->isMethod('post'))
    {
      $this->form->bind($request->getParameter($this->form->getName()));

      if ($this->form->isValid())
      {
        $values = $this->form->getValues();

        $user->setPassword($values['password']);
        $user->save();
        $this->redirect('@homepage');
      }
    }
  }

  /**
   * Returns new password change form
   *
   * @return sfForm
   */
  protected function getPasswordForm()
  {
    return new sfObjectGuardPasswordForm;
  }
}
