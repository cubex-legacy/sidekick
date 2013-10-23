<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Fortify\Controllers\FortifyController;
use Sidekick\Components\Users\Enums\UserRole;

class FortifyApp extends BaseApp
{
  public function name()
  {
    return "Fortify";
  }

  public function description()
  {
    return "Code Build & Testing";
  }

  public function defaultController()
  {
    return new FortifyController();
  }

  public function getNavGroup()
  {
    return "Development";
  }

  public function getBundles()
  {
    return [
      //      new DebuggerBundle()
    ];
  }

  public function getRoutes()
  {
    return [
      'builds/(.*)'        => 'FortifyBuildsController',
      'commands/(.*)'      => 'FortifyCommandsController',
      'buildCommands/(.*)' => 'FortifyBuildCommandsController',
      'repository/(.*)'    => 'FortifyRepositoryController'
    ];
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
