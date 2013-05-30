<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Phuse\Views\PackageView;

class PhuseController extends BaseControl
{
  public function renderIndex()
  {
    return new PackageView();
  }
}
