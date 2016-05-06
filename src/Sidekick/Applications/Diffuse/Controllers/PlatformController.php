<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 10:26
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Diffuse\Views\Platforms\DeploymentConfigurationIndex;
use Sidekick\Components\Diffuse\Mappers\DeploymentConfig;
use Sidekick\Components\Fortify\Mappers\Build;

class PlatformController extends DiffuseController
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
    $form = new Form('createPlatform', '');
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
    $msg->text = 'Platform was successfully created - ';

    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function renderEdit()
  {
    $platformId    = $this->getInt('platformId');
    $platform      = new DeploymentConfig($platformId);

    $form = new Form('editPlatform', '');
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
    $msg->text = 'Platform was successfully updated - ';

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
    $msg->text = 'Platform was successfully deleted';

    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function getRoutes()
  {
    return [
      '/create'             => 'create',
      '/:platformId/edit'   => 'edit',
      '/:platformId/delete' => 'delete'
    ];
  }
}
