<?php
/**
* @author: brooke.bryan
* Application: BaseApp
*/

namespace Sidekick\Applications\BaseApp;

use Cubex\Core\Application\Application;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Themed\Sidekick\SidekickTheme;

class SidekickApplication extends Application
{
  public function __construct()
  {
    $this->_listen(__NAMESPACE__);
  }

  /**
   * @return \Sidekick\Project
   */
  public function project()
  {
    return parent::project();
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

  public function userPermitted($userRole)
  {
    return false;
  }
}
