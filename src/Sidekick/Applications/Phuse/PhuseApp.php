<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Phuse\Controllers\ComposedController;
use Sidekick\Applications\Phuse\Controllers\DefaultController;

class PhuseApp extends BaseApp
{
  protected $_composer;

  public function __construct($composer = false)
  {
    $this->_composer = $composer;
  }

  public function defaultController()
  {
    if($this->_composer)
    {
      return new ComposedController();
    }
    else
    {
      return new DefaultController();
    }
  }

  public function getNavGroup()
  {
    return "Development";
  }

  public function name()
  {
    return "Phuse"; //Phusing all the parts of your project together
  }

  public function description()
  {
    return "Package Manager";
  }
}
