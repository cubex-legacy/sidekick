<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;

class PhuseController extends BaseControl
{
  public function renderIndex()
  {
    return "My Phuse App";
  }
}
