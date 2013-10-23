<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\PreviewApp;

use Cubex\Core\Application\Application;
use Sidekick\Applications\PreviewApp\Controllers\PreviewController;

class PreviewApp extends Application
{
  public function defaultController()
  {
    return new PreviewController();
  }

  public function userPermitted($userRole)
  {
    return true;
  }
}
