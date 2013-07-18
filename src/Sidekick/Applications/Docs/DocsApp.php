<?php
/**
 * Author: oke.ugwu
 * Date: 17/07/13 15:36
 */

namespace Sidekick\Applications\Docs;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Docs\Controllers\DefaultController;

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
}
