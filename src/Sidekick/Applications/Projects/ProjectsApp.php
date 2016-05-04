<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Projects;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\Projects\Controllers\DefaultController;
use Sidekick\Components\Users\Enums\UserRole;

class ProjectsApp extends SidekickApplication
{
  public function defaultController()
  {
    return new DefaultController();
  }

  public function name()
  {
    return "Projects";
  }

  public function description()
  {
    return "Projects Manager";
  }

  public function getNavGroup()
  {
    return "Menu";
  }

  public function getBundles()
  {
    return [
      //new DebuggerBundle()
    ];
  }

  public function getRoutes()
  {
    return [
      'build-configs/commands/(.*)'    => '\Sidekick\Applications\Fortify'
        . '\Controllers\FortifyCommandsController',
      'build-configs/(.*)'    => '\Sidekick\Applications\Fortify'
        . '\Controllers\FortifyBuildsController',
      'manage-hosts/(.*)'     => '\Sidekick\Applications\Diffuse'
        . '\Controllers\HostController',
      'manage-platforms/(.*)' => '\Sidekick\Applications\Diffuse'
        . '\Controllers\PlatformController',
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
