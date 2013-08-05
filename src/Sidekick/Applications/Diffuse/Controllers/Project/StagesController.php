<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Project;

use Cubex\Facade\Redirect;
use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use Sidekick\Applications\Diffuse\Views\Project\DeploymentStages;
use Sidekick\Applications\Diffuse\Views\Project\ManageDeploymentStages;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;

class StagesController extends DiffuseController
{
  public function renderIndex()
  {
    $projectId = $this->getInt("projectId");
    return new DeploymentStages($projectId, DeploymentStage::collection(
      ['project_id' => $projectId]
    ));
  }

  public function renderCreate()
  {
    return new ManageDeploymentStages();
  }

  public function renderEdit()
  {
    $stageID = $this->getInt("stageId");
    $stage   = DeploymentStage::collection()->loadOneWhere(["id" => $stageID]);
    return new ManageDeploymentStages($stage);
  }

  public function postCreate()
  {
    //Create configuration object
    $cKeys  = $this->_request->postVariables("configurationKeys");
    $cVals  = $this->_request->postVariables("configurationValues");
    $config = new \StdClass();
    for($i = 0; $i < count($cKeys); $i++)
    {
      $config->{$cKeys[$i]} = $cVals[$i];
    }
    //Create dependencies object
    $dKeys = $this->_request->postVariables("dependencyKeys");
    $dVals = $this->_request->postVariables("dependencyValues");
    $deps  = new \StdClass();
    for($i = 0; $i < count($dKeys); $i++)
    {
      $deps->{$dKeys[$i]} = $dVals[$i];
    }
    $stage = new DeploymentStage();
    $stage->hydrate(
      [
      "platform_id"            => $this->_request->postVariables("platform"),
      "project_id"             => $this->getInt("projectId"),
      "service_class"          => $this->_request->postVariables(
        "serviceClass"
      ),
      "require_all_hosts_pass" => $this->_request->postVariables(
        "requireAllHostsPass"
      ),
      "configuration"          => $config,
      "dependencies"           => $deps
      ]
    );
    $stage->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Deployment Stage created successfully';
    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function postEdit()
  {
    //Create configuration object
    $cKeys  = $this->_request->postVariables("configurationKeys");
    $cVals  = $this->_request->postVariables("configurationValues");
    $config = new \StdClass();
    for($i = 0; $i < count($cKeys); $i++)
    {
      $config->{$cKeys[$i]} = $cVals[$i];
    }
    //Create dependencies object
    $dKeys = $this->_request->postVariables("dependencyKeys");
    $dVals = $this->_request->postVariables("dependencyValues");
    $deps  = new \StdClass();
    for($i = 0; $i < count($dKeys); $i++)
    {
      $deps->{$dKeys[$i]} = $dVals[$i];
    }
    $stage = new DeploymentStage();
    $stage->hydrate(
      [
      "id"                     => $this->_request->postVariables("id"),
      "platform_id"            => $this->_request->postVariables("platform"),
      "project_id"             => $this->getInt("projectId"),
      "service_class"          => $this->_request->postVariables(
        "serviceClass"
      ),
      "require_all_hosts_pass" => $this->_request->postVariables(
        "requireAllHostsPass"
      ),
      "configuration"          => $config,
      "dependencies"           => $deps
      ]
    );
    $stage->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Deployment Stage updated successfully';
    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function renderDelete()
  {
    $stageID = $this->getInt("stageId");
    $stage   = DeploymentStage::collection()->loadOneWhere(["id" => $stageID]);
    $stage->delete();
    $msg       = new \stdClass();
    $msg->type = "success";
    $msg->text = "Stage deleted successfully";
    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function getRoutes()
  {
    return [
      '/new'                 => 'create',
      '/:stageId@num/edit'   => 'edit',
      '/:stageId@num/delete' => 'delete',
    ];
  }
}
