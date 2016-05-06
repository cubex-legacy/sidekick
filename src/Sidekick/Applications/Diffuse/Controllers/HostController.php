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
use Sidekick\Applications\Diffuse\Views\Hosts\HostsIndex;
use Sidekick\Components\Servers\Mappers\Server;

class HostController extends DiffuseController
{
  public function preRender()
  {
    parent::preRender();
    $this->requireCss('diffuse');
  }

  public function renderIndex()
  {
    $hosts = Server::collection()->loadAll();
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
    $host = new Server();
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
    $platformId = $this->getInt('serverId');
    $host       = new Server($platformId);

    $form = new Form('editPlatform', '');
    $form->addHiddenElement('id', $host->id());
    $form->addTextElement('name', $host->name);
    $form->addTextElement('hostname', $host->hostname);
    $form->addTextElement('ipv4', $host->ipv4);
    $form->addTextElement('ipv6', $host->ipv6);
    $form->addTextElement('sshUser', $host->sshUser);
    $form->addNumberElement('sshPort', $host->sshPort);
    $form->addSubmitElement('Update');

    return new RenderGroup(
      '<h1>Edit Host</h1>',
      $form
    );
  }

  public function postEdit()
  {
    $host = new Server();
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
    $serverId = $this->getInt('serverId');
    $host   = new Server($serverId);
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
      '/:serverId/edit'   => 'edit',
      '/:serverId/delete' => 'delete'
    ];
  }
}
