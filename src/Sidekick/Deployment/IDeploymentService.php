<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment;

use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;
use Sidekick\Components\Diffuse\Mappers\Version;

interface IDeploymentService
{
  /**
   * @param Version         $version
   * @param DeploymentStage $stage
   */
  public function __construct(Version $version, DeploymentStage $stage);

  /**
   * @param DeploymentStageHost $host
   *
   * @return self
   */
  public function addHost(DeploymentStageHost $host);

  /**
   * @return DeploymentStageHost[]
   */
  public function getHosts();

  /**
   * @param $hostId
   *
   * @return DeploymentStageHost
   */
  public function getHost($hostId);

  /**
   * @return void
   */
  public function deploy();
}
