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
use Sidekick\Components\Fortify\Mappers\Build;

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

    $build = Build::collection()->loadAll()->getKeyPair('id', 'name');

    $form->addTextElement('configuration');
    $form->addCheckboxElements('requiredBuilds[]', '', $build);
    $form->addRadioElements('requireApproval', 1, ['No', 'Yes']);
    $form->addSubmitElement('Create');

    $form->getElement('requiredBuilds[]')->setLabel('Required Builds');

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
    $platform   = new Platform($platformId);

    $form = new Form('editPlatform', '');
    $form->addHiddenElement('id', $platform->id());
    $form->addTextElement('name', $platform->name);
    $form->addTextareaElement('description', $platform->description);
    $form->addSelectElement(
      'transportType',
      (new OptionBuilder(new TransportType))->getOptions(),
      $platform->transportType
    );

    $build         = Build::collection()->loadAll()->getKeyPair('id', 'name');
    $selectedBuild = [];
    if(is_array($platform->requiredBuilds))
    {
      $selectedBuild = Build::collection()->loadIds($platform->requiredBuilds)
                       ->getUniqueField('id');
    }

    $form->addTextElement('configuration', $platform->configuration);
    $form->addCheckboxElements('requiredBuilds[]', $selectedBuild, $build);
    $form->addRadioElements(
      'requireApproval',
      $platform->requireApproval,
      ['No', 'Yes']
    );
    $form->addSubmitElement('Update');

    $form->getElement('requiredBuilds[]')->setLabel('Required Builds');

    return new RenderGroup(
      '<h1>Edit Platform</h1>',
      $form
    );
  }

  public function postEdit()
  {
    $platform = new Platform();
    $platform->hydrateFromUnserialized($this->request()->postVariables());
    $platform->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Platform was successfully updated';
    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
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
