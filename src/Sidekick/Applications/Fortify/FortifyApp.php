<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify;

use Sidekick\Applications\BaseApp\BaseApp;

use Sidekick\Applications\Fortify\Controllers\FortifyCrudController;
use Sidekick\Applications\Fortify\Controllers\FortifyController;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\Command;

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
    return new FortifyController();
  }

  public function getRoutes()
  {
    return [
      'builds/(.*)'   => new FortifyCrudController(
        new Build(), ['name', 'description', 'build_level', 'source_directory']
      ),
      'commands/(.*)' => new FortifyCrudController(
        new Command(), ['id', 'name', 'command']
      )
    ];
  }
}
