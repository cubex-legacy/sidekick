<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Deployments;

use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use
  Sidekick\Applications\Diffuse\Views\Projects\Deployments\DeploymentDetailsView;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;

class DeploymentController extends DiffuseController
{
  public function renderIndex()
  {
    $deploymentId = $this->getInt('deploymentId');
    $deployment = new Deployment($deploymentId);

    $deploymentStagesHosts = DeploymentStageHost::collection(
      ['deployment_id' => $deploymentId]
    );

    $deploymentStages = DeploymentStage::collection(
      ['platform_id' => $deployment->platformId]
    );

    return new DeploymentDetailsView(
      $deployment, $deploymentStages, $deploymentStagesHosts
    );
  }
}
