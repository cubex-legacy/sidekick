<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 10:26
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\DeploymentConfigStepsView;
use Sidekick\Applications\Diffuse\Views\DeploymentConfigurationIndex;
use Sidekick\Applications\Diffuse\Views\ManageDeploymentStepsView;
use Sidekick\Components\Diffuse\Mappers\DeploymentConfig;
use Sidekick\Components\Diffuse\Mappers\DeploymentStep;

class DeploymentConfigController extends DiffuseController
{
  public function preRender()
  {
    parent::preRender();
    $this->requireCss('diffuse');
  }

  public function renderIndex()
  {
    $platforms = DeploymentConfig::collection()->loadAll();
    return $this->createView(new DeploymentConfigurationIndex($platforms));
  }

  public function renderCreate()
  {
    $form = new Form('createDeploymentConfig', '');
    $form->addTextElement('name');
    $form->addTextareaElement('description');
    $form->addSubmitElement('Create');

    return new RenderGroup(
      '<h1>Create Deployment Configuration</h1>',
      $form
    );
  }

  public function postCreate()
  {
    $platform = new DeploymentConfig();
    $platform->hydrate($this->request()->postVariables());
    $platform->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Deployment Config was successfully created - ';

    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function renderEdit()
  {
    $platformId = $this->getInt('platformId');
    $platform   = new DeploymentConfig($platformId);

    $form = new Form('editDeploymentConfiguration', '');
    $form->addHiddenElement('id', $platform->id());
    $form->addTextElement('name', $platform->name);
    $form->addTextareaElement('description', $platform->description);
    $form->addSubmitElement('Update');
    return new RenderGroup(
      '<h1>Edit Deployment Configuration</h1>',
      $form
    );
  }

  public function postEdit()
  {
    $platform = new DeploymentConfig();
    $platform->hydrateFromUnserialized($this->request()->postVariables());
    $platform->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Deployment Configuration was successfully updated - ';

    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function renderDelete()
  {
    $platformId = $this->getInt('platformId');
    $platform   = new DeploymentConfig($platformId);
    $platform->delete();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Deployment Configuration was successfully deleted';

    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function renderStepsIndex()
  {
    if($this->getInt('configId'))
    {
      $stages    = DeploymentStep::collection();
      $platforms = DeploymentConfig::collection()->loadOneWhere(
        ['id' => $this->getInt('configId')]
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

  public function renderNewSteps()
  {
    $stage          = new DeploymentStep();
    $view           = new ManageDeploymentStepsView($stage);
    $view->configId = $this->getInt('configId');
    return $view;
  }

  public function postNewSteps()
  {
    $this->_createOrUpdateSteps();

    $redirectUrl = $this->baseUri() . '/' . $this->getInt('configId')
      . '/steps';

    Redirect::to($redirectUrl)->with(
      'msg',
      new TransportMessage('success', 'Deployment Step created successfully')
    )->now();
  }

  public function renderEditSteps()
  {
    $stepId = $this->getInt("stepId");
    $step   = new DeploymentStep($stepId);

    return new ManageDeploymentStepsView($step);
  }

  public function postEditSteps()
  {
    $this->_createOrUpdateSteps();

    Redirect::to($this->baseUri())->with(
      'msg',
      new TransportMessage('success', 'Deployment Step updated successfully')
    )->now();
  }

  private function _createOrUpdateSteps()
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

  public function renderDestroySteps()
  {
    $stepId = $this->getInt("stepId");
    $step   = new DeploymentStep($stepId);
    $step->delete();

    $redirectUrl = $this->baseUri() . '/' . $this->getInt('configId')
      . '/steps';
    Redirect::to($redirectUrl)->with(
      'msg',
      new TransportMessage('success', 'Deployment Step deleted successfully')
    )->now();
  }

  public function renderOrderSteps()
  {
    $stepId     = $this->getInt('stepId');
    $configId = $this->getInt('configId');
    $direction  = $this->getStr('direction');

    $stage    = new DeploymentStep($stepId);
    $oldOrder = $stage->order;

    $lastOrder = DeploymentStep::collection(
      ['platform_id' => $configId]
    )->count();

    if($oldOrder == 1 && $direction == 'up'
      || $oldOrder == $lastOrder && $direction == 'down'
    )
    {
      // Invalid Order Action
      Redirect::to($this->baseUri() . '/' . $configId . '/steps')->now();
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
            'platform_id' => $configId,
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
    Redirect::to($this->baseUri() . '/' . $configId . '/steps')->now();
  }

  public function getRoutes()
  {
    return [
      '/create'                                   => 'create',
      '/:platformId/edit'                         => 'edit',
      '/:platformId/delete'                       => 'delete',
      '/:configId/steps'                          => 'stepsIndex',
      '/:configId/steps/new'                      => 'newSteps',
      '/:configId/steps/:stepId/edit'             => 'editSteps',
      '/:configId/steps/:stepId/delete'           => 'destroySteps',
      '/:configId/steps/:stepId/order/:direction' => 'orderSteps',
    ];
  }
}
