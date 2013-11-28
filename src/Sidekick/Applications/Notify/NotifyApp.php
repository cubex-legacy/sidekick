<?php
/**
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify;

use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\Notify\Controllers\NotifyController;

class NotifyApp extends SidekickApplication
{

  public function defaultController()
  {
    return new NotifyController();
  }

  public function name()
  {
    return "Notify";
  }

  public function description()
  {
    return "Notify Application";
  }

  public function getBundles()
  {
    return [
      //new DebuggerBundle()
    ];
  }

  public function getNavGroup()
  {
    return "Configuration";
  }

  public function userPermitted($userRole)
  {
    return true;
  }

  public function getRoutes()
  {
    return [
      '/'                   => 'NotifyController',
      '/subscriptions/(.*)' => 'NotifySubscriptionsController',
    ];
  }
}
