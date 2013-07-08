<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment;

use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;
use Sidekick\Components\Diffuse\Mappers\Version;

abstract class BaseDeploymentService implements IDeploymentService
{
  protected $_version;
  protected $_stage;
  /**
   * @var DeploymentStageHost[]
   */
  protected $_hosts;

  public function __construct(Version $version, DeploymentStage $stage)
  {
    $this->_version = $version;
    $this->_stage   = $stage;
  }

  /**
   * @param DeploymentStageHost $host
   *
   * @return self
   */
  public function addHost(DeploymentStageHost $host)
  {
    $this->_hosts[$host->hostId] = $host;
    return $this;
  }

  /**
   * @return DeploymentStageHost[]
   */
  public function getHosts()
  {
    return $this->_hosts;
  }

  /**
   * @param $hostId
   *
   * @return DeploymentStageHost
   */
  public function getHost($hostId)
  {
    return isset($this->_hosts[$hostId]) ? $this->_hosts[$hostId] : null;
  }
}
