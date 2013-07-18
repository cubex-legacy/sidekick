<?php
/**
* @author: brooke.bryan
* Application: BaseApp
*/

namespace Sidekick\Applications\BaseApp;

use Cubex\Core\Application\Application;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Themed\Sidekick\SidekickTheme;

class BaseApp extends Application
{
  public function __construct()
  {
    $this->_listen(__NAMESPACE__);
  }

  public function getRoutes()
  {
    return [
    ];
  }

  public function getNavGroup()
  {
    return null;
  }

  public function defaultController()
  {
    return new BaseControl();
  }

  public function getTheme()
  {
    return new SidekickTheme();
  }
}
