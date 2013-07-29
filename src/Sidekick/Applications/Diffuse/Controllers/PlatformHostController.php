<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 29/07/13
 * Time: 11:09
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\PlatformHost;
use Sidekick\Components\Diffuse\Mappers\Host;
use Sidekick\Components\Diffuse\Mappers\HostPlatform;
use Sidekick\Components\Diffuse\Mappers\Platform;

class PlatformHostController extends DiffuseController
{

  public function renderIndex()
  {
    $render = new RenderGroup();
    $render->add(new HtmlElement("h1", [], "Platform Hosts"));
    $render->add(new HtmlElement("p", [], "Select a project"));
    return $render;
  }

  public function renderManage()
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
      $this->baseUri() . "/" . $project
    )
    ->with("msg", $msg)->now();
  }

  public function renderEdit()
  {
    $platformId   = $this->getInt("platformId");
    $projectId    = $this->getInt("projectId");
    $hostplatform = HostPlatform::collection()->loadOneWhere(
      ["platform_id" => $platformId, "project_id" => $projectId]
    );
    $form         = new Form("EditForm");
    $form->addHiddenElement("project", $this->getInt("projectId"));
    $form->addHiddenElement("platform", $this->getInt("platformId"));
    $form->addSelectElement(
      "host",
      (new OptionBuilder(Host::collection()))->getOptions(),
      $hostplatform->host_id
    );
    $form->addSubmitElement("Update Platform Host");
    $render = new RenderGroup();
    $render->add(new HtmlElement("h1", [], "Edit Platform Host"));
    $render->add($form);
    return $render;
  }

  public function postEdit()
  {
    $host                  = $this->_request->postVariables("host");
    $platform              = $this->_request->postVariables("platform");
    $project               = $this->_request->postVariables("project");
    $hostplatform          = HostPlatform::collection()->loadOneWhere(
      ["platform_id" => $platform, "project_id" => $project]
    );
    $hostplatform->host_id = $host;
    $hostplatform->saveChanges();
    $msg       = new \stdClass();
    $msg->type = "success";
    $msg->text = "Platform Host updated successfully";
    Redirect::to(
      $this->baseUri() . "/" . $project
    )
    ->with("msg", $msg)->now();
  }

  public function renderDelete()
  {
    $platformId   = $this->getInt("platformId");
    $projectId    = $this->getInt("projectId");
    $hostplatform = HostPlatform::collection()->loadOneWhere(
      ["platform_id" => $platformId, "project_id" => $projectId]
    );
    $hostplatform->delete();
    $msg       = new \stdClass();
    $msg->type = "success";
    $msg->text = "Platform Host deleted successfully";
    Redirect::to(
      $this->baseUri() . "/" . $projectId
    )
    ->with("msg", $msg)->now();
  }

  public function getRoutes()
  {
    return [
      "/:projectId"                    => "manage",
      "/:projectId/add"                => "add",
      "/:projectId/:platformId/edit"   => "edit",
      "/:projectId/:platformId/delete" => "delete"
    ];
  }
}
