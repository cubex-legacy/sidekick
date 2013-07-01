<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Users;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Users\Controllers\DefaultController;

class UsersApp extends BaseApp
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
}
