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
  Sidekick\Applications\Diffuse\Views\Projects\Configuration\DeploymentConfigStepsView;
use
  Sidekick\Applications\Diffuse\Views\Projects\Configuration\ManageDeploymentStepsView;
use Sidekick\Components\Diffuse\Mappers\DeploymentStep;
use Sidekick\Components\Diffuse\Mappers\DeploymentConfig;

class DeploymentController extends DiffuseController
{
  public function renderIndex()
  {
    if($this->getInt('id'))
    {
      $stages    = DeploymentStep::collection();
      $platforms = DeploymentConfig::collection()->loadOneWhere(
        ['id' => $this->getInt('id')]
      );

      return new RenderGroup(
        $this->createView(
          new DeploymentConfigStepsView($platforms, $stages)
        )
      );
    }
    else
    {
      echo "You need to select a deployment config to add steps to";
    }
  }

  public function renderNew()
  {
    $stage = new DeploymentStep();
    return new ManageDeploymentStepsView($stage);
  }

  public function postNew()
  {
    $this->_createOrUpdate();

    Redirect::to('/' . $this->baseUri())->with(
      'msg',
      new TransportMessage('success', 'Deployment Stage created successfully')
    )->now();
  }

  public function renderEdit()
  {
    $stageId = $this->getInt("id");
    $stage   = new DeploymentStep($stageId);

    return new ManageDeploymentStepsView($stage);
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

    if($postData['id'])
    {
      $step = new DeploymentStep($postData["id"]);
    }
    else
    {
      $step = new DeploymentStep();
      //get max order and plus one it
      $lastStep    = DeploymentStep::collection(
        [
          'platform_id' => $postData['platformId']
        ]
      )->setOrderBy('order', 'DESC')->first();
      $step->order = idp($lastStep, "order", 0) + 1;
    }

    $step->platformId = $postData["platformId"];
    $step->name       = $postData["name"];
    $step->command    = $postData["command"];
    $step->saveChanges();
  }

  public function renderDestroy()
  {
    $stageId = $this->getInt("id");
    $stage   = new DeploymentStep($stageId);
    $stage->delete();

    Redirect::to($this->baseUri())->with(
      'msg',
      new TransportMessage('success', 'Stage deleted successfully')
    )->now();
  }

  public function renderOrder()
  {
    $stageId    = $this->getInt('id');
    $platformId = $this->getInt('platformId');
    $direction  = $this->getStr('direction');

    $stage    = new DeploymentStep($stageId);
    $oldOrder = $stage->order;

    $lastOrder = DeploymentStep::collection(
      ['platform_id' => $platformId]
    )->count();

    if($oldOrder == 1 && $direction == 'up'
      || $oldOrder == $lastOrder && $direction == 'down'
    )
    {
      // Invalid Order Action
      Redirect::to('/' . $this->baseUri())->now();
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
        $swapStage = DeploymentStep::collection()->loadWhere(
          [
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
    Redirect::to('/' . $this->baseUri() . '/' . $platformId . '/steps')->now();
  }

  public function getRoutes()
  {
    $routes = ResourceTemplate::getRoutes();
    $routes[] = new StdRoute('/:id/steps', 'index');
    $routes[] = new StdRoute('/:id/getConfigurationOptions', 'configOptions');
    $routes[] = new StdRoute('/:id/:platformId/order/:direction', 'order');
    return $routes;
  }
}
