<?php
/**
* @author: brooke.bryan
* Application: BaseApp
*/

namespace Sidekick\Applications\BaseApp;

use Cubex\Core\Application\Application;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;

class BaseApp extends Application
{
  public function getRoutes()
  {
    return [
    ];
  }

  public function defaultController()
  {
    return new BaseControl();
  }
}
