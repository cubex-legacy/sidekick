<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 29/07/13
 * Time: 11:09
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Controllers\Project;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use Sidekick\Applications\Diffuse\Views\Project\PlatformHost;
use Sidekick\Components\Diffuse\Mappers\Host;
use Sidekick\Components\Diffuse\Mappers\HostPlatform;
use Sidekick\Components\Diffuse\Mappers\Platform;

class HostsController extends DiffuseController
{

  public function renderIndex()
  {
    return new PlatformHost($this->getInt("projectId"));
  }

  public function renderAdd()
  {
    $form = new Form("AddForm");
    $form->addHiddenElement("project", $this->getInt("projectId"));
    $form->addSelectElement(
      "platform",
      (new OptionBuilder(Platform::collection()))->getOptions()
    );
    $form->addSelectElement(
      "host",
      (new OptionBuilder(Host::collection()))->getOptions()
    );
    $form->addSubmitElement("Create Platform Host");
    $render = new RenderGroup();
    $render->add(new HtmlElement("h1", [], "Add Platform Host"));
    $render->add($form);
    return $render;
  }

  public function postAdd()
  {
    $host         = $this->_request->postVariables("host");
    $platform     = $this->_request->postVariables("platform");
    $project      = $this->_request->postVariables("project");
    $hostplatform = new HostPlatform();
    $hostplatform->hydrate(
      [
      "host_id"     => $host,
      "platform_id" => $platform,
      "project_id"  => $project
      ]
    );
    $hostplatform->saveChanges();
    $msg       = new \stdClass();
    $msg->type = "success";
    $msg->text = "Platform Host created successfully";
    Redirect::to(
      $this->baseUri()
    )
    ->with("msg", $msg)->now();
  }

  public function renderDelete()
  {
    $platformId   = $this->getInt("platformId");
    $projectId    = $this->getInt("projectId");
    $hostId       = $this->getInt("hostId");
    $hostplatform = HostPlatform::collection()->loadOneWhere(
      [
      "platform_id" => $platformId,
      "project_id"  => $projectId,
      "host_id"     => $hostId
      ]
    );
    $hostplatform->delete();
    $msg       = new \stdClass();
    $msg->type = "success";
    $msg->text = "Platform Host deleted successfully";
    Redirect::to(
      $this->baseUri()
    )
    ->with("msg", $msg)->now();
  }

  public function getRoutes()
  {
    return [
      "/"                           => "index",
      "/new"                        => "add",
      "/:platformId/:hostId/delete" => "delete"
    ];
  }
}
