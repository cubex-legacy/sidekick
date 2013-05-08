<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Repository\Controllers\DefaultController;

class RepositoryApp extends BaseApp
{
  public function name()
  {
    return "Repository";
  }

  public function description()
  {
    return "Version Control";
  }

  public function defaultController()
  {
    return new DefaultController();
  }
}
