<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment;

use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Users\Mappers\User;

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
   * @param $serverId
   *
   * @return DeploymentStageHost
   */
  public function getHost($serverId);

  /**
   * @return void
   */
  public function deploy();

  public function setUser(User $user);

  /**
   * Return a keyed array of configuration items.
   *
   * key = configuration item name
   * value = description of configuration item
   *
   * @return array|null
   */
  public static function getConfigurationItems();
}
