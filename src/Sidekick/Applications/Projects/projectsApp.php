<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Projects;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Projects\Controllers\DefaultController;

class projectsApp extends BaseApp
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
}