<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick;

use Cubex\Core\Application\Application;
use Sidekick\Applications\Diffuse\DiffuseApp;
use Sidekick\Applications\Dispatcher\DispatcherApp;

class Project extends \Cubex\Core\Project\Project
{
  protected $_apps;

  protected function _configure()
  {
    $this->addApplication('diffuse', new DiffuseApp());
    $this->addApplication('dispatcher', new DispatcherApp());
  }

  public function addApplication($path, Application $application)
  {
    $this->_apps[$path] = $application;
    return $this;
  }

  /**
   * Project Name
   *
   * @return string
   */
  public function name()
  {
    return "Cubex Sidekick";
  }

  /**
   * @return \Cubex\Core\Application\Application[]
   */
  public function getApplications()
  {
    return $this->_apps;
  }

  public function getByPath($path)
  {
    $apps = $this->getApplications();
    foreach($apps as $appPath => $app)
    {
      if(strpos($path, '/' . $appPath) === 0)
      {
        return new $app();
      }
    }
    return null;
  }

  /**
   * @return \Cubex\Core\Application\Application
   */
  public function defaultApplication()
  {
    return new Applications\BaseApp\BaseApp();
  }
}
