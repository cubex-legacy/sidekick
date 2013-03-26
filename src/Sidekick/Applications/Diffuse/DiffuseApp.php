<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Diffuse\Controllers\DefaultController;

class DiffuseApp extends BaseApp
{
  public function defaultController()
  {
    return new DefaultController();
  }

  public function name()
  {
    return "Diffuse";
  }

  public function description()
  {
    return "Code Distribution";
  }
}
