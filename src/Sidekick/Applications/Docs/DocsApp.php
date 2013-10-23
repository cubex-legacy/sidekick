<?php
/**
 * Author: oke.ugwu
 * Date: 17/07/13 15:36
 */

namespace Sidekick\Applications\Docs;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Docs\Controllers\DefaultController;
use Sidekick\Components\Users\Enums\UserRole;

class DocsApp extends BaseApp
{
  public function name()
  {
    return "API Docs";
  }

  public function description()
  {
    return "Docs generated with API Gen";
  }

  public function defaultController()
  {
    return new DefaultController();
  }

  public function getNavGroup()
  {
    return "Documentation";
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
