<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Api;

use Sidekick\Applications\BaseApp\SidekickApplication;

class ApiApp extends SidekickApplication
{
  public function getRoutes()
  {
    return [
      'fortify/builds/(.*)' => 'Fortify\Builds',
      'rosetta/(.*)'        => 'Rosetta\Translate',
    ];
  }

  public function getBundles()
  {
    return [];
  }

  public function name()
  {
    return "API";
  }

  public function description()
  {
    return "Sidekick API";
  }
}
