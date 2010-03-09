<?php

/**
 *
 * @package    sfDoctrineObjectGuardPlugin
 * @subpackage plugin
 * @author     Konstantin Kudryashov <ever.zet@gmail.com>
 */
class sfObjectGuardBasicSecurityFilter extends sfBasicSecurityFilter
{
  protected $object;

  public function execute($filterChain)
  {
    $user = $this->getContext()->getUser();

    if ($this->isFirstCall() and !$user->isAuthenticated())
    {
      if ($cookie = $this->getCookie())
      {
        $q = Doctrine_Query::create()
              ->from('sfObjectGuardRememberKey r')
              ->innerJoin('r.sfObjectGuardUser u')
              ->where('r.remember_key = ?', $cookie);

        if ($q->count())
        {
          $user->signIn($q->fetchOne()->sfObjectGuardUser);
        }
      }
    }

    parent::execute($filterChain);
  }

  protected function getCookie()
  {
    return $this->getContext()->
      getRequest()->
      getCookie(sfConfig::get('app_sf_com_guard_plugin_remember_cookie_name', 'sfRemember'));
  }

  protected function getRouteObject()
  {
    if (is_null($this->object))
    {
      $route = $this->getContext()->getRequest()->getAttribute('sf_route');
      if ($route instanceof sfDoctrineRoute)
      {
        $this->object = $route->getObject();
      }
    }

    return $this->object;
  }

  protected function getUserCredential()
  {
    $credential = parent::getUserCredential();

    if (!is_null($this->getRouteObject()))
    {
      $credential = is_array($credential) ? $credential : array($credential);
      foreach ($credential as $key => $value)
      {
        $credential[$key] = str_ireplace('/id/', '/' . $this->getRouteObject()->getId() . '/', $value);
      }
    }

    return $credential;
  }
}