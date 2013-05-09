<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\BaseApp\Controllers\CrudController;
use Sidekick\Applications\Fortify\Controllers\DefaultController;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\BuildCommand;

class FortifyApp extends BaseApp
{
  public function name()
  {
    return "Fortify";
  }

  public function description()
  {
    return "Code Build & Testing";
  }

  public function defaultController()
  {
    return new DefaultController();
  }

  public function getRoutes()
  {
    return [
      'builds/(.*)'   => new CrudController(new Build()),
      'commands/(.*)' => new CrudController(
        new BuildCommand(), ['id', 'name', 'command']
      )
    ];
  }
}
