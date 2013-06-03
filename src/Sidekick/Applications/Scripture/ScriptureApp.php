<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Scripture;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Scripture\Controllers\ScriptureController;

class ScriptureApp extends BaseApp
{
  protected $_composer;

  public function __construct($composer = false)
  {
    $this->_composer = $composer;
  }

  public function defaultController()
  {
    return new ScriptureController();
  }

  public function name()
  {
    return "Scripture";
  }

  public function description()
  {
    return "Documentation";
  }

  public function getRoutes()
  {
    return [
      '' => 'ScriptureController',
    ];
  }
}