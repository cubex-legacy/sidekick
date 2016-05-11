<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\ProjectAwareBaseControl;
use Sidekick\Applications\Diffuse\Views\DeploymentLogsView;
use Sidekick\Applications\Diffuse\Views\DeploymentsView;
use Sidekick\Applications\Diffuse\Views\DeploymentView;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\DeploymentConfig;
use Sidekick\Components\Diffuse\Mappers\DeploymentLog;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Servers\Mappers\Server;

class OverviewController extends ProjectAwareBaseControl
{
  protected $_projectId;

  public function preProcess()
  {
    $this->_projectId = $this->getProjectId();
  }

  public function getSidebar()
  {
    return null;
  }

  public function renderIndex()
  {
    $this->requireJs('deploymentPage');
    $project = new Project($this->_projectId);
    if($project->exists())
    {
      $hosts   = Server::collection();
      $configs = DeploymentConfig::collection();

      $buildRun = new BuildRun($this->getInt('buildId'));

      return new RenderGroup(
        $this->createView(
          new DeploymentView($project, $hosts, $configs, $buildRun)
        )
      );
    }
    else
    {
      throw new \Exception("You seem to have stumbed upon.... nothing.");
    }
  }

  public function postIndex()
  {
    $postData = $this->request()->postVariables();
    if(isset($postData['platformId']) && (int)$postData['platformId'] > 0
      && count($postData['deploymentHosts'])
      && !empty($postData['deploy_base'])
    )
    {
      //create a new deployment
      $deployment             = new Deployment();
      $deployment->pending    = true;
      $deployment->platformId = $postData["platformId"]; //TODO rename to deploymentConfigId
      $deployment->projectId  = $this->_projectId;
      $deployment->buildId    = $this->getInt("buildId");
      $deployment->userId     = \Auth::user()->getId();
      $deployment->hosts      = json_encode(
        array_keys($postData['deploymentHosts'])
      );
      $deployment->comment    = $postData['comment'];
      $deployment->deployBase = $postData['deploy_base'];

      $br = new BuildRun($deployment->buildId);
      $deployment->branch = $br->branch;

      $deployment->saveChanges();

      //TODO refactor how versions work. This has been put here to
      //make our new deployment work with out version errors
      //get the latest version
      $latestVersion               = Version::collection()
        ->loadWhere(['project_id' => $this->_projectId])
        ->setOrderBy('id', 'DESC')
        ->first();
      $latestVersion->versionState = 'approved';
      $latestVersion->SaveChanges();

      $stateId           = [$postData["platformId"], $latestVersion->id()];
      $state             = new PlatformVersionState($stateId);
      $state->platformId = $postData["platformId"];
      $state->versionId  = $latestVersion->id();
      $state->deploymentCount++;
      $state->state = 'approved';
      $state->saveChanges();

      Redirect::to('/P' . $this->_projectId . '/diffuse/deployments')->now();
    }
    else
    {
      $redirectUrl = '/P' . $this->_projectId . '/diffuse/'
        . $this->getInt('buildId');
      Redirect::to($redirectUrl)->with(
        'msg',
        new TransportMessage(
          'error',
          'Please make sure you have selected a deployment configuration'
          . ' and at least one server'
        )
      )->now();
    }
  }

  public function renderDeployments()
  {
    $deployments = Deployment::collection();
    $project     = null;
    if($this->_projectId)
    {
      $project = new Project($this->_projectId);
      if($project->exists())
      {
        $deployments = Deployment::collection()->loadWhere(
          "project_id = %d",
          $this->_projectId
        )->setOrderBy(
          'created_at',
          'DESC'
        )->setLimit(0, 20);
      }
    }
    else
    {
      $deploymentIds = Deployment::conn()->getKeyedRows(
        'SELECT max(id) as id FROM diffuse_deployments '
        . 'GROUP BY project_id'
      );

      $deployments = Deployment::collection()->loadIds(
        array_keys($deploymentIds)
      )->setOrderBy('created_at', 'DESC');
    }

    return new DeploymentsView($deployments, $project);
  }

  public function renderDeploymentLogs()
  {
    $deploymentId   = $this->getInt('id');
    $deploymentLogs = DeploymentLog::collection()->loadWhere(
      "deployment_id = %d",
      $deploymentId
    );

    return new DeploymentLogsView(
      $deploymentLogs, new Deployment($deploymentId)
    );
  }

  public function getRoutes()
  {
    return [
    ];
  }
}
