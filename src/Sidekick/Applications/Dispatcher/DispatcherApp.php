<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Dispatcher;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Dispatcher\Controllers\DefaultController;

class DispatcherApp extends BaseApp
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
