<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Configurator\Controllers\DefaultController;

class ConfiguratorApp extends BaseApp
{
  public function name()
  {
    return "Configurator";
  }

  public function description()
  {
    return "Config Distribution";
  }

  public function defaultController()
  {
    return new DefaultController();
  }

  public function getBundles()
  {
    return [
      /*new DebuggerBundle()*/
    ];
  }

  public function getRoutes()
  {
    return [
      '/environments'      => 'EnvironmentsController',
      '/environments/(.*)' => 'EnvironmentsController',
    ];
  }
}
