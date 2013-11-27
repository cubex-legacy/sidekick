<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Configuration;

use Cubex\Core\Http\Response;
use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Routing\StdRoute;
use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use
Sidekick\Applications\Diffuse\Views\Projects\Configuration\DeploymentConfigurationOptionsView;
use
Sidekick\Applications\Diffuse\Views\Projects\Configuration\DeploymentConfigurationView;
use
Sidekick\Applications\Diffuse\Views\Projects\Configuration\ManageDeploymentStagesView;
use Sidekick\Applications\Diffuse\Views\Projects\ProjectNav;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Projects\Mappers\Project;

class DeploymentController extends DiffuseController
{
  public function renderIndex()
  {
    $projectId = $this->getProjectId();
    $project   = new Project($projectId);
    $stages    = DeploymentStage::collection(['project_id' => $projectId]);
    $platforms = Platform::orderedCollection();

    return new RenderGroup(
      $this->createView(new ProjectNav($this->appBaseUri(), $project)),
      $this->createView(
        new DeploymentConfigurationView($project, $platforms, $stages)
      )
    );
  }

  public function renderNew()
  {
    $stage = new DeploymentStage();
    return new ManageDeploymentStagesView($stage);
  }

  public function postNew()
  {
    $this->_createOrUpdate();

    Redirect::to($this->baseUri())->with(
      'msg',
      new TransportMessage('success', 'Deployment Stage created successfully')
    )->now();
  }

  public function renderEdit()
  {
    $stageId = $this->getInt("id");
    $stage   = new DeploymentStage($stageId);

    return new ManageDeploymentStagesView($stage);
  }

  public function postEdit()
  {
    $this->_createOrUpdate();

    Redirect::to($this->baseUri())->with(
      'msg',
      new TransportMessage('success', 'Deployment Stage updated successfully')
    )->now();
  }

  private function _createOrUpdate()
  {
    $postData = $this->request()->postVariables();

    //Create configuration object
    $config = new \StdClass();
    foreach($postData["configuration"] as $key => $value)
    {
      $config->$key = $value;
    }

    if($postData['id'])
    {
      $stage = new DeploymentStage($postData["id"]);
    }
    else
    {
      $stage = new DeploymentStage();
      //get max order and plus one it
      $projectId    = $this->getProjectId();
      $lastStage    = DeploymentStage::collection(
        [
        'project_id'  => $projectId,
        'platform_id' => $postData['platformId']
        ]
      )->setOrderBy('order', 'DESC')->first();
      $stage->order = idp($lastStage, "order", 0) + 1;
    }

    $stage->platformId          = $postData["platformId"];
    $stage->projectId           = $this->getProjectId();
    $stage->serviceClass        = $postData["serviceClass"];
    $stage->requireAllHostsPass = $postData["requireAllHostsPass"];
    $stage->configuration       = $config;
    $stage->saveChanges();
  }

  public function renderDestroy()
  {
    $stageId = $this->getInt("id");
    $stage   = new DeploymentStage($stageId);
    $stage->delete();

    Redirect::to($this->baseUri())->with(
      'msg',
      new TransportMessage('success', 'Stage deleted successfully')
    )->now();
  }

  public function postConfigOptions()
  {
    $serviceClass = $this->request()->postVariables('serviceClass');

    $stageId = $this->getInt("id");
    $stage   = new DeploymentStage($stageId);

    return new Response(
      new DeploymentConfigurationOptionsView(
        $serviceClass,
        $stage->configuration
      )
    );
  }

  public function renderOrder()
  {
    $stageId    = $this->getInt('id');
    $projectId  = $this->getProjectId();
    $platformId = $this->getInt('platformId');
    $direction  = $this->getStr('direction');

    $stage    = new DeploymentStage($stageId);
    $oldOrder = $stage->order;

    $lastOrder = DeploymentStage::collection(
      ['project_id' => $projectId, 'platform_id' => $platformId]
    )->count();

    if($oldOrder == 1 && $direction == 'up'
    || $oldOrder == $lastOrder && $direction == 'down'
    )
    {
      // Invalid Order Action
      Redirect::to($this->baseUri())->now();
    }
    else
    {
      $oldOrder  = (int)$oldOrder;
      $swapOrder = null;
      switch($direction)
      {
        case 'up':
          $swapOrder = $oldOrder - 1;
          $swapOrder = ($swapOrder < 1) ? 0 : $swapOrder;
          break;
        case 'down':
          $swapOrder = $oldOrder + 1;
          break;
      }

      if($swapOrder !== null)
      {
        $swapStage = DeploymentStage::collection()->loadWhere(
          [
          'project_id'  => $projectId,
          'platform_id' => $platformId,
          'order'       => $swapOrder
          ]
        )->first();
        if($swapStage !== null)
        {
          $swapStage->order = $oldOrder;
          $swapStage->saveChanges();
        }

        $stage->order = $swapOrder;
        $stage->saveChanges();
      }
    }
    Redirect::to($this->baseUri())->now();
  }

  public function getRoutes()
  {
    $routes = ResourceTemplate::getRoutes();
    array_unshift(
      $routes,
      new StdRoute('/getConfigurationOptions', 'configOptions')
    );
    $routes[] = new StdRoute('/:id/getConfigurationOptions', 'configOptions');
    $routes[] = new StdRoute('/:id/:platformId/order/:direction', 'order');
    return $routes;
  }
}
