<?php
/**
 * Author: oke.ugwu
 * Date: 15/07/13 09:53
 */

namespace Sidekick\Applications\Login;

use Cubex\Core\Application\Application;
use Sidekick\Applications\Login\Controllers\DefaultController;
use Themed\Sidekick\SidekickTheme;

class LoginApp extends Application
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

  public function defaultController()
  {
    return new DefaultController();
  }

  public function getTheme()
  {
    return new SidekickTheme();
  }
}
