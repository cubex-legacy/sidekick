<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository;

use Sidekick\Applications\BaseApp\ProjectAwareApplication;
use Sidekick\Applications\Repository\Controllers\DefaultController;
use Sidekick\Components\Users\Enums\UserRole;

class RepositoryApp extends ProjectAwareApplication
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

  public function getRoutes()
  {
    return [
      '/branch/:branchId/commits' => 'CommitsController'
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
