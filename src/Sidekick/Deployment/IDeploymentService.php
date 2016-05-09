<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment;

use Sidekick\Components\Diffuse\Mappers\DeploymentStep;
use Sidekick\Components\Diffuse\Mappers\DeploymentLog;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Users\Mappers\User;

interface IDeploymentService
{
  /**
   * @param Version         $version
   * @param DeploymentStep $stage
   */
  public function __construct(Version $version, DeploymentStep $stage);

  /**
   * @param DeploymentLog $host
   *
   * @return self
   */
  public function addHost(DeploymentLog $host);

  /**
   * @return DeploymentLog[]
   */
  public function getHosts();

  /**
   * @param $serverId
   *
   * @return DeploymentLog
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
