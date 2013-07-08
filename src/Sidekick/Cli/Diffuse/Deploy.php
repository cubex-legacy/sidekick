<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;
use Cubex\Log\Log;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;
use Sidekick\Components\Diffuse\Mappers\HostPlatform;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Deployment\IDeploymentService;

class Deploy extends CliCommand
{
  /**
   * @valuerequired
   */
  public $projectId;
  /**
   * @valuerequired
   */
  public $version;
  /**
   * @valuerequired
   */
  public $platform;

  protected $_echoLevel = 'debug';

  public function execute()
  {
    $version = new Version($this->version);
    if(!$version->exists())
    {
      $version->major = 1;
      $version->saveChanges();
      Log::info("Created version " . $version->id());
    }

    $platform = new Platform($this->platform);
    if(!$platform->exists())
    {
      $platform->name = 'Test platform';
      $platform->saveChanges();
      Log::info("Created platform " . $platform->id());
    }

    $deployment             = new Deployment();
    $deployment->platformId = $platform->id();
    $deployment->versionId  = $version->id();
    $deployment->saveChanges();

    $hosts = HostPlatform::collectionOn($platform)->preFetch("host");
    if(!$hosts)
    {
      throw new \Exception("No Hosts have been assigned to this platform");
    }

    $stages    = DeploymentStage::collection(
      ['platform_id' => $platform->id()]
    );
    $passStage = true;
    foreach($stages as $stage)
    {
      if(!$passStage)
      {
        throw new \Exception("Unable to proceed, as previous stage failed.");
      }
      /**
       * @var $stage DeploymentStage
       */
      $deployService = $stage->serviceClass;
      if(class_exists($deployService))
      {
        Log::info("Deploying with '$deployService'");
        $diffuser = new $deployService($version, $stage);

        if($diffuser instanceof IDeploymentService)
        {
          foreach($hosts as $hostPlat)
          {
            /**
             * @var $hostPlat \Sidekick\Components\Diffuse\Mappers\HostPlatform
             */
            $stageHost                    = new DeploymentStageHost();
            $stageHost->hostId            = $hostPlat->host()->id();
            $stageHost->deploymentId      = $deployment->id();
            $stageHost->deploymentStageId = $stage->id();
            $diffuser->addHost($stageHost);
          }

          $diffuser->deploy();

          $hostResults = $diffuser->getHosts();
          foreach($hostResults as $hostResult)
          {
            if($stage->requireAllHostsPass && !$hostResult->passed)
            {
              $passStage = false;
            }
            $hostResult->saveChanges();
          }
        }
      }
      else
      {
        throw new \Exception("The class '$deployService' does not exist");
      }
    }
  }
}
