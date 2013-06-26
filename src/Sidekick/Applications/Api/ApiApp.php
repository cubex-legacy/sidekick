<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Api;

use Sidekick\Applications\BaseApp\BaseApp;

class ApiApp extends BaseApp
{
  public function getRoutes()
  {
    return [
      'fortify/builds/(.*)' => 'Fortify\Builds',
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
