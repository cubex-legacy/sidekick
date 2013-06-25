<?php
/**
 * Author: oke.ugwu
 * Date: 25/06/13 11:02
 */

namespace Sidekick\Applications\SourceCode;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\SourceCode\Controllers\DefaultController;

class SourceCodeApp extends BaseApp
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
}
