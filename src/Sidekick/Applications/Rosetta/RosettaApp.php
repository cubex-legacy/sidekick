<?php
/**
 * @author Luke.Rodham
 */

namespace Sidekick\Applications\Rosetta;

use Sidekick\Applications\BaseApp\ProjectAwareApplication;
use Sidekick\Applications\Rosetta\Controllers\DefaultController;

class RosettaApp extends ProjectAwareApplication
{
  protected $_composer;

  public function __construct($composer = false)
  {
    $this->_composer = $composer;
  }

  public function defaultController()
  {
    return new DefaultController();
  }

  public function name()
  {
    return "Rosetta";
  }

  public function getNavGroup()
  {
    return "Menu";
  }

  public function description()
  {
    return "Translation Manager";
  }

  public function getRoutes()
  {
    return [];
  }

  public function userPermitted($userRole)
  {
    return true;
  }
}
