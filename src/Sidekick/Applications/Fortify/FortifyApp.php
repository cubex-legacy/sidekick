<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify;

use Sidekick\Applications\BaseApp\ProjectAwareApplication;
use Sidekick\Applications\Fortify\Controllers\FortifyHomeController;
use Sidekick\Components\Users\Enums\UserRole;

class FortifyApp extends ProjectAwareApplication
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
    return new FortifyHomeController();
  }

  public function getNavGroup()
  {
    return "Menu";
  }

  public function getRoutes()
  {
    return [
      'build-configs'               => 'FortifyBuildsController',
      'build-configs/builds/(.*)'   => 'FortifyBuildsController',
      'build-configs/commands'      => 'FortifyBuildsController@renderCommandsIndex',
      'build-configs/commands/(.*)' => 'FortifyCommandsController',
      'build-configs/projects'      => 'FortifyBuildsController@renderProjects',
      'build-configs/aprojects'     => 'FortifyBuildsController@postProjects',
      'buildCommands/(.*)'          => 'FortifyBuildCommandsController',
      'repository/(.*)'             => 'FortifyRepositoryController'
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
