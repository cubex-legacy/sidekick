<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Dispatcher\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;

class DefaultController extends BaseControl
{
  public function renderIndex()
  {
    echo "My Dispatcher App";
  }
}
