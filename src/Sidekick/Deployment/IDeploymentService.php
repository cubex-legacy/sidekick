<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment;

use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;
use Sidekick\Components\Diffuse\Mappers\Version;

interface IDeploymentService
{
  public function __construct(Version $version, DeploymentStageHost $stage);

  /**
   * @return bool success
   */
  public function deploy();
}
