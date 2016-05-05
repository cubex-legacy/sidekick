<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Overview;

use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\Overview\Controllers\OverviewController;

class OverviewApp extends SidekickApplication
{
  protected $_composer;

  public function __construct($composer = false)
  {
    $this->_composer = $composer;
  }

  public function defaultController()
  {
    return new OverviewController();
  }

  public function name()
  {
    return "Overview";
  }

  public function description()
  {
    return "Sidekick Overview";
  }

  public function userPermitted($userRole)
  {
    return true;
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
      'projects/(.*)'     => '\Sidekick\Applications\Projects'
        . '\Controllers\DefaultController',
    ];
  }
}
