<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Log\Log;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;
use Sidekick\Components\Diffuse\Mappers\HostPlatform;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Users\Mappers\User;
use Sidekick\Deployment\IDeploymentService;

class Deploy extends CliCommand
{
  /**
   * @valuerequired
   */
  public $versionId;

  /**
   * @valuerequired
   */
  public $userId;

  public $verbose;

  /**
   * @valuerequired
   */
  public $platformId;

  protected $_echoLevel = 'debug';

  public function execute()
  {
    $version = new Version($this->versionId);
    if(!$version->exists())
    {
      throw new \Exception("The version specified does not exist");
    }

    $platform = new Platform($this->platformId);
    if(!$platform->exists())
    {
      throw new \Exception("The platform specified does not exist");
    }

    $project = new Project($version->projectId);
    if(!$project->exists())
    {
      throw new \Exception("The project specified does not exist");
    }

    $user = new User($this->userId);
    if(!$user->exists())
    {
      throw new \Exception("The user specified does not exist");
    }

    $this->_createVersionDataFile($version);

    $deployment             = new Deployment();
    $deployment->platformId = $platform->id();
    $deployment->versionId  = $version->id();
    $deployment->projectId  = $project->id();
    $deployment->userId     = $user->id();
    $deployment->saveChanges(); //Initiate deployment for the ID

    $hosts = HostPlatform::collection(
      [
      "platform_id" => $platform->id(),
      "project_id"  => $project->id()
      ]
    )
    ->preFetch("server");

    if(!$hosts)
    {
      $deployment->completed = 1;
      $deployment->saveChanges();
      throw new \Exception("No Hosts have been assigned to this platform");
    }

    $stages    = DeploymentStage::collection(
      [
      'platform_id' => $platform->id(),
      'project_id'  => $project->id(),
      ]
    );
    $passStage = true;
    foreach($stages as $stage)
    {
      if(!$passStage)
      {
        $deployment->completed = 1;
        $deployment->saveChanges();
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
            $stageHost->serverId            = $hostPlat->serverId;
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
        $deployment->completed = 1;
        $deployment->saveChanges();
        throw new \Exception("The class '$deployService' does not exist");
      }
    }

    $stateId           = [$platform->id(), $version->id()];
    $state             = new PlatformVersionState($stateId);
    $state->platformId = $platform->id();
    $state->versionId  = $version->id();
    $state->deploymentCount++;
    $state->saveChanges();

    $deployment->passed    = 1;
    $deployment->completed = 1;
    $deployment->saveChanges();
  }

  protected function _createVersionDataFile(Version $v)
  {
    $sourcePath = VersionHelper::sourceLocation($v);
    $filePath   = $sourcePath . 'DIFFUSE.VERSION';
    file_put_contents($filePath, $this->_versionFile($v));
  }

  protected function _versionFile(Version $v)
  {
    $content = '';
    $content .= 'Version: ' . $v->format() . "\n";
    $content .= 'Commits: ' . $v->fromCommitHash . ' - ' . $v->toCommitHash;
    $content .= "\n";
    $content .= 'State: ' . $v->versionState . "\n";
    $content .= 'Project ID: ' . $v->projectId . "\n";
    $content .= 'Build ID: ' . $v->buildId . "\n";
    $content .= 'Repository ID: ' . $v->repoId . "\n";
    $content .= 'Created: ' . $v->getData($v->createdAttribute()) . "\n";
    $content .= 'Updated: ' . $v->getData($v->updatedAttribute()) . "\n";
    $content .= "\n== Change Log ==\n";
    $content .= $v->changeLog;
    return $content;
  }
}
