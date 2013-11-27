<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Scripture;

use Sidekick\Applications\BaseApp\ProjectAwareApplication;
use Sidekick\Applications\Scripture\Controllers\ScriptureController;
use Sidekick\Components\Users\Enums\UserRole;

class ScriptureApp extends ProjectAwareApplication
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

  public function name()
  {
    return "Scripture";
  }

  public function description()
  {
    return "Documentation";
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
