<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Deployment\IDeploymentService;

class Deploy extends CliCommand
{
  public $projectId;
  public $version;
  public $platform;

  public function execute()
  {
    $version  = new Version($this->version);
    $platform = new Platform($this->platform);

    $deployment             = new Deployment();
    $deployment->platformId = $platform->id();
    $deployment->versionId  = $version->id();
    $deployment->saveChanges();

    $stages = DeploymentStage::collection(['platform_id' => $platform->id()]);
    foreach($stages as $stage)
    {
      /**
       * @var $stage DeploymentStage
       */
      $deployService = $stage->serviceClass;
      if(class_exists($deployService))
      {
        $stageHosts = DeploymentStageHost::collection(
          ['deployment_stage_id' => $stage->id()]
        );

        if(!$stageHosts)
        {
          throw new \Exception("No Hosts");
        }

        foreach($stageHosts as $stageHost)
        {
          $diffuser = new $deployService($version, $stageHost);
          if($diffuser instanceof IDeploymentService)
          {
            $diffuser->deploy();
          }
        }
      }
    }
  }
}
