<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Configuration;

use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use
Sidekick\Applications\Diffuse\Views\Projects\Configuration\DeploymentConfigurationView;

class DeploymentController extends DiffuseController
{
  public function renderIndex()
  {
    return new DeploymentConfigurationView();
  }
}
