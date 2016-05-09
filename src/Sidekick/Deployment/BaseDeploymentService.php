<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment;

use Sidekick\Components\Diffuse\Mappers\DeploymentStep;
use Sidekick\Components\Diffuse\Mappers\DeploymentLog;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Users\Mappers\User;

abstract class BaseDeploymentService implements IDeploymentService
{
  /**
   * @var User
   */
  protected $_user;
  protected $_version;
  protected $_stage;
  /**
   * @var DeploymentLog[]
   */
  protected $_hosts;

  public function __construct(Version $version, DeploymentStep $stage)
  {
    $this->_version = $version;
    $this->_stage   = $stage;
  }

  /**
   * @param DeploymentLog $host
   *
   * @return self
   */
  public function addHost(DeploymentLog $host)
  {
    $this->_hosts[$host->serverId] = $host;
    return $this;
  }

  /**
   * @return DeploymentLog[]
   */
  public function getHosts()
  {
    return $this->_hosts;
  }

  /**
   * @param $serverId
   *
   * @return DeploymentLog
   */
  public function getHost($serverId)
  {
    return isset($this->_hosts[$serverId]) ? $this->_hosts[$serverId] : null;
  }

  public function setUser(User $user)
  {
    $this->_user = $user;
    return $this;
  }

  public static function getConfigurationItems()
  {
    return [];
  }
}
