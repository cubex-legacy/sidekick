<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Diffuse\Views\Sidebar;

class DiffuseController extends BaseControl
{
  public function getSidebar()
  {
    return new Sidebar();
  }
}
