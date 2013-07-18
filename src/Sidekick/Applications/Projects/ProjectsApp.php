<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Projects;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Projects\Controllers\DefaultController;

class ProjectsApp extends BaseApp
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
    return "Configuration";
  }

  public function getBundles()
  {
    return [
//      new DebuggerBundle()
    ];
  }

  public function getRoutes()
  {

  }
}
