<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 10:26
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\HostsIndex;
use Sidekick\Components\Diffuse\Mappers\Host;

class HostsController extends DiffuseController
{
  public function renderIndex()
  {
    $hosts = Host::collection()->loadAll();
    return $this->createView(new HostsIndex($hosts));
  }

  public function renderCreate()
  {

    $form = new Form('createPlatform', '');
    $form->addTextElement('name');
    $form->addTextElement('hostname');
    $form->addTextElement('ipv4');
    $form->addTextElement('ipv6');
    $form->addSubmitElement('Create');

    return new RenderGroup(
      '<h1>Create Platform</h1>',
      $form
    );
  }

  public function postCreate()
  {
    $host = new Host();
    $host->hydrate($this->request()->postVariables());
    $host->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Host was successfully created';
    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function renderEdit()
  {
    $platformId = $this->getInt('hostId');
    $host       = new Host($platformId);

    $form = new Form('editPlatform', '');
    $form->addHiddenElement('id', $host->id());
    $form->addTextElement('name', $host->name);
    $form->addTextElement('hostname', $host->hostname);
    $form->addTextElement('ipv4', $host->ipv4);
    $form->addTextElement('ipv6', $host->ipv6);
    $form->addSubmitElement('Update');

    return new RenderGroup(
      '<h1>Edit Host</h1>',
      $form
    );
  }

  public function postEdit()
  {
    $host = new Host();
    $host->hydrate($this->request()->postVariables());
    $host->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Host was successfully updated';
    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function renderDelete()
  {
    $hostId = $this->getInt('hostId');
    $host   = new Host($hostId);
    $host->delete();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Host was successfully deleted';
    Redirect::to($this->baseUri())->with(
      'msg',
      $msg
    )->now();
  }

  public function getRoutes()
  {
    return [
      '/create'         => 'create',
      '/edit/:hostId'   => 'edit',
      '/delete/:hostId' => 'delete'
    ];
  }
}
