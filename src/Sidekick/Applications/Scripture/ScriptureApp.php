<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Scripture;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\Scripture\Controllers\ScriptureController;
use Sidekick\Components\Users\Enums\UserRole;

class ScriptureApp extends SidekickApplication
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

  public function getNavGroup()
  {
    return "Documentation";
  }

  public function getBundles()
  {
    return [
      //new DebuggerBundle()
    ];
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

  public function userPermitted($userRole)
  {
    if($userRole == UserRole::USER)
    {
      return false;
    }
    return true;
  }
}
