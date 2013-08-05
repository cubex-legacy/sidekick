<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 14:15
 */

namespace Sidekick\Applications\Diffuse\Controllers\Project;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use Sidekick\Applications\Diffuse\Views\Project\ApprovalConfigurationPage;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Sidekick\Enums\Consistency;
use Sidekick\Components\Users\Enums\UserRole;

class ApprovalController extends DiffuseController
{
  public function preRender()
  {
    parent::preRender();

    $this->requireJsLibrary('jquery');
    $this->requireJs('approvalConfig');
  }

  public function renderIndex()
  {
    return new RenderGroup(
      "<h1>Approval Configuration</h1>",
      "<p>Select a project on the left to see the approval configuration</p>"
    );
  }

  public function renderApproval()
  {
    $projectId = $this->getInt('projectId');
    return new ApprovalConfigurationPage($projectId);
  }

  public function renderConfigure()
  {
    $project  = $this->getInt("projectId");
    $platform = $this->getInt("platform");
    $acForm   = new Form("ApprovalConfiguration");
    $acForm->addHiddenElement("project_id", $project);
    $acForm->addHiddenElement("platform_id", $platform);
    $acForm->addSelectElement(
      "role",
      (new OptionBuilder(new UserRole))->getOptions()
    );
    $acForm->addSelectElement(
      "consistency_level",
      (new OptionBuilder(new Consistency()))->getOptions()
    );
    $acForm->addSelectElement("required", ["No", "Yes"]);
    $acForm->addSubmitElement("Create Configuration");
    return new RenderGroup("<h1>Create Configuration</h1>", $acForm);
  }

  public function postConfigure()
  {
    $ac = new ApprovalConfiguration();
    $ac->hydrate($this->request()->postVariables());
    $ac->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Configuration created successfully';
    Redirect::to($this->baseUri())->with('msg', $msg)
    ->now();
  }

  public function renderDelete()
  {
    $platform = $this->getInt("platform");
    $project  = $this->getInt("projectId");
    $role     = $this->getStr("role");
    $ac       = ApprovalConfiguration::collection()->loadOneWhere(
      ["platform_id" => $platform, "project_id" => $project, "role" => $role]
    );
    if($ac !== null)
    {
      $ac->delete();
    }
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Configuration deleted successfully';
    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function getRoutes()
  {
    return [
      '/'                       => 'approval',
      '/:platform'              => 'approval',
      '/:platform/new'          => 'configure',
      '/:platform/:role/delete' => 'delete',
    ];
  }
}
