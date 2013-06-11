<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Fortify\Controllers\FortifyController;

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
}
