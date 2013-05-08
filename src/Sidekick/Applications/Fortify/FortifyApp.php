<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Fortify\Controllers\DefaultController;

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
}
