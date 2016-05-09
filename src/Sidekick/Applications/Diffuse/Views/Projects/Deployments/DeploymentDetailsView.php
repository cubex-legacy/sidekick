<?php
/**
 * Author: oke.ugwu
 * Date: 10/09/13 13:53
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Deployments;

use Cubex\View\TemplatedViewModel;

class DeploymentDetailsView extends TemplatedViewModel
{
  protected $_deployment;
  protected $_deploymentStages;
  protected $_deploymentStagesHosts;

  public function __construct(
    $deployment, $deploymentStages, $deploymentStagesHosts
  )
  {
    $this->_deployment = $deployment;
    $this->_deploymentStages = $deploymentStages;
    $this->_deploymentStagesHosts = $deploymentStagesHosts;
  }

  /**
   * @return \Sidekick\Components\Diffuse\Mappers\Deployment
   */
  public function getDeployment()
  {
    return $this->_deployment;
  }

  /**
   * @return \Sidekick\Components\Diffuse\Mappers\DeploymentStep
   */
  public function getDeploymentStages()
  {
    return $this->_deploymentStages;
  }

  /**
   * @return \Sidekick\Components\Diffuse\Mappers\DeploymentLog[]
   */
  public function getDeploymentDetail()
  {
    return $this->_deploymentStagesHosts;
  }

  public function getUser()
  {
    $user = $this->getDeployment()->user();
    if($user->exists())
    {
      return $user->displayName;
    }
    return 'Unknown';
  }
}
