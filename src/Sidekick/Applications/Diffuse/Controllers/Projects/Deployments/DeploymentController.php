<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Deployments;

use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use
  Sidekick\Applications\Diffuse\Views\Projects\Deployments\DeploymentDetailsView;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\DeploymentStep;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;

class DeploymentController extends DiffuseController
{
  public function renderIndex()
  {
    $deploymentId = $this->getInt('deploymentId');
    $deployment   = new Deployment($deploymentId);

    $deploymentStagesHosts = DeploymentStageHost::collection(
      ['deployment_id' => $deploymentId]
    );

    $deploymentStages = DeploymentStep::collection(
      [
        'platform_id' => $deployment->platformId,
        'project_id'  => $this->getProjectId()
      ]
    );

    return new DeploymentDetailsView(
      $deployment, $deploymentStages, $deploymentStagesHosts
    );
  }
}
