<?php
/**
 * Author: oke.ugwu
 * Date: 25/06/13 11:02
 */

namespace Sidekick\Applications\SourceCode;

use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\SourceCode\Controllers\DefaultController;
use Sidekick\Components\Users\Enums\UserRole;

class SourceCodeApp extends SidekickApplication
{
  public function name()
  {
    return "Source Code";
  }

  public function description()
  {
    return "Display source in prettify";
  }

  public function defaultController()
  {
    return new DefaultController();
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
