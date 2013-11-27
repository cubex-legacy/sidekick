<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\Repository\Controllers\DefaultController;
use Sidekick\Components\Users\Enums\UserRole;

class RepositoryApp extends SidekickApplication
{
  public function name()
  {
    return "Repository";
  }

  public function description()
  {
    return "Version Control";
  }

  public function defaultController()
  {
    return new DefaultController();
  }

  public function getNavGroup()
  {
    return "Configuration";
  }

  public function getBundles()
  {
    //return [new DebuggerBundle()];
  }

  public function getRoutes()
  {
    return [
      '/commits'      => 'CommitsController',
      '/commits/(.*)' => 'CommitsController'
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
