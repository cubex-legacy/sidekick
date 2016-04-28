<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Users;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\Users\Controllers\DefaultController;
use Sidekick\Components\Users\Enums\UserRole;

class UsersApp extends SidekickApplication
{
  public function defaultController()
  {
    return new DefaultController();
  }

  public function name()
  {
    return "Users";
  }

  public function description()
  {
    return "Users Manager";
  }

  public function getNavGroup()
  {
    return "Menu";
  }

  public function getBundles()
  {
    return [
      // new DebuggerBundle()
    ];
  }

  public function getRoutes()
  {
    return [];
  }

  public function userPermitted($userRole)
  {
    if($userRole == UserRole::USER)
    {
      return false;
    }
    return true;
  }
}
