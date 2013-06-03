<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Overview;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Overview\Controllers\OverviewController;

class OverviewApp extends BaseApp
{
  protected $_composer;

  public function __construct($composer = false)
  {
    $this->_composer = $composer;
  }

  public function defaultController()
  {
    return new OverviewController();
  }

  public function name()
  {
    return "Overview";
  }

  public function description()
  {
    return "Sidekick Overview";
  }
}
