<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Repository\Controllers\DefaultController;

class RepositoryApp extends BaseApp
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

  public function getBundles()
  {
    //    return [new DebuggerBundle()];
  }

  public function getRoutes()
  {
    //this is here as a temp fix for bug http://phabricator.cubex.io/T105
    return ['/' => ''];
  }
}
