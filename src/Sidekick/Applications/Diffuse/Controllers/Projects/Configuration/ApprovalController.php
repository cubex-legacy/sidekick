<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Configuration;

use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use
Sidekick\Applications\Diffuse\Views\Projects\Configuration\ApprovalConfigurationView;

class ApprovalController extends DiffuseController
{
  public function renderIndex()
  {
    return new ApprovalConfigurationView();
  }
}
