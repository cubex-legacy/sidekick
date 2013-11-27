<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Dispatcher;

use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\Dispatcher\Controllers\DefaultController;

class DispatcherApp extends SidekickApplication
{
  public function defaultController()
  {
    return new DefaultController();
  }

  public function name()
  {
    return "Dispatcher";
  }

  public function description()
  {
    return "Task Scheduler";
  }
}
