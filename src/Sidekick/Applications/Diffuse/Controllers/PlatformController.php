<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 10:26
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Cubex\Form\OptionBuilder;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\PlatformIndex;
use Sidekick\Components\Diffuse\Enums\TransportType;
use Sidekick\Components\Diffuse\Mappers\Platform;

class PlatformController extends DiffuseController
{
  public function renderIndex()
  {
    $platforms = Platform::collection()->loadAll();
    return $this->createView(new PlatformIndex($platforms));
  }

  public function renderCreate()
  {
    $form = new Form('createPlatform', '');
    $form->addTextElement('name');
    $form->addTextareaElement('description');
    $form->addSelectElement(
      'transportType',
      (new OptionBuilder(new TransportType))->getOptions()
    );
    $form->addTextElement('configuration');
    $form->addTextElement('requiredBuilds');
    $form->addRadioElements('requireApproval', 1, ['No', 'Yes']);
    $form->addSubmitElement('Create');

    return new RenderGroup(
      '<h1>Create Platform</h1>',
      $form
    );
  }

  public function postCreate()
  {
    $platform = new Platform();
    $platform->hydrate($this->request()->postVariables());
    $platform->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Platform was successfully created';
    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function renderEdit()
  {
    $platformId = $this->getInt('platformId');
    return '<h1>Coming Soon</h1>';
  }

  public function renderDelete()
  {
    $platformId = $this->getInt('platformId');
    $platform   = new Platform($platformId);
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
      '/edit/:platformId'   => 'edit',
      '/delete/:platformId' => 'delete'
    ];
  }
}
