<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 24/07/13
 * Time: 10:05
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Controllers\Project;

use Cubex\Facade\Redirect;
use Cubex\Queue\StdQueue;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Applications\Diffuse\Views\Project\VersionPlatform;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Sidekick\Enums\Consistency;

class VersionsPlatformController extends DiffuseProjectController
{
  public function renderIndex()
  {
    Redirect::to("/diffuse")->now();
  }

  public function renderPlatform()
  {
    $projectID  = $this->getInt("projectId");
    $versionID  = $this->getInt("versionId");
    $platformID = $this->getInt("platform");
    $platform   = Platform::collection()->loadOneWhere(["id" => $platformID]);
    $nav        = $this->getNav($platform->name);
    if($platform == null)
    {
      $render = new RenderGroup();
      $render->add(new HTMLElement("h1", [], "Platform: " . $platform->name));
      $render->add($nav);
      $render->add(
        new HTMLElement("div", ["class" => "alert alert-error"], "This platform does not exist")
      );
      return $render;
    }

    //Check dependencies here
    foreach($platform->requiredPlatforms as $required)
    {
      $pvs = PlatformVersionState::collection()->loadOneWhere(
        ["platform_id" => $required, "version_id" => $versionID]
      );
      if($pvs == null || $pvs->state != VersionState::APPROVED)
      {
        $failedPlatform = Platform::collection()->loadOneWhere(
          ["id" => $required]
        );
        $render         = new RenderGroup();
        $render->add(new HTMLElement("h1", [], "Platform: " . $platform->name));
        $render->add($nav);
        $render->add(
          new HTMLElement("div", ["class" => "alert alert-error"], "This platform requires " . $failedPlatform->name . " to be approved before it can be deployed to")
        );
        return $render;
      }
    }
    return new VersionPlatform($platformID, $projectID, $versionID, $nav);
  }

  public function postPlatform()
  {
    $role                 = $this->_request->postVariables("role");
    $action               = $this->_request->postVariables("Action");
    $comment              = $this->_request->postVariables("comment");
    $projectID            = $this->getInt("projectId");
    $versionID            = $this->getInt("versionId");
    $platformID           = $this->getInt("platform");
    $platformVersionState = new PlatformVersionState();
    $platformVersionState->hydrate(
      [
      "platform_id" => $platformID,
      "version_id"  => $versionID
      ]
    );
    if($action !== ActionType::COMMENT)
    {
      $rolesCollection = ProjectUser::collection()->loadOneWhere(
        ["project_id" => $projectID, "user_id" => \Auth::user()->getId()]
      );
      $roles           = ($rolesCollection == null) ? [] : $rolesCollection->roles;
      if(!in_array($role, $roles))
      { //They're not who they claim to be
        $msg       = new \stdClass();
        $msg->type = "error";
        $msg->text = "You are not a $role on this project";
        Redirect::to(
          $this->baseUri() .
          "/" . $projectID . "/v/" . $versionID . "/p/" . $platformID
        )
        ->with("msg", $msg)->now();
      }
      $previouslyPerformed = Action::collection()->loadWhere(
        [
        "platform_id" => $platformID,
        "version_id"  => $versionID,
        "user_id"     => \Auth::user()->getId(),
        "user_role"   => $role,
        "action_type" => $action
        ]
      );
      if($previouslyPerformed->count() != 0)
      {
        $msg       = new \stdClass();
        $msg->type = "error";
        $msg->text = "You have already performed '$action' on this project";
        Redirect::to(
          $this->baseUri(
          ) . "/" . $projectID . "/" . $versionID . "/" . $platformID
        )
        ->with("msg", $msg)->now();
      }
      $thisAction = new Action();
      $thisAction->hydrate(
        [
        "platform_id" => $platformID,
        "version_id"  => $versionID,
        "user_id"     => \Auth::user()->getId(),
        "user_role"   => $role,
        "action_type" => $action,
        "comment"     => $comment
        ]
      );
      $thisAction->saveChanges();
      //Update the PlatformVersionState
      $newState                    = $this->requirementState(
        $platformID,
        $projectID,
        $versionID
      );
      $platformVersionState->state = $newState;
      $platformVersionState->saveChanges();
      $msg       = new \stdClass();
      $msg->type = "success";
      $msg->text = "Action executed successfully";
      Redirect::to(
        $this->baseUri() .
        "/" . $projectID . "/v/" . $versionID . "/p/" . $platformID
      )
      ->with("msg", $msg)->now();
    }
    else //Comments
    {
      //Check PlatformVersionState
      $newState                    = $this->requirementState(
        $platformID,
        $projectID,
        $versionID
      );
      $platformVersionState->state = $newState;
      $platformVersionState->saveChanges();
      $commentAction = new Action();
      $commentAction->hydrate(
        [
        "platform_id" => $platformID,
        "version_id"  => $versionID,
        "user_id"     => \Auth::user()->getId(),
        "action_type" => ActionType::COMMENT,
        "user_role"   => $role,
        "comment"     => $comment
        ]
      );
      $commentAction->saveChanges();
      $msg       = new \stdClass();
      $msg->type = "success";
      $msg->text = "Comment added successfully";
      Redirect::to(
        $this->baseUri() .
        "/" . $projectID . "/v/" . $versionID . "/p/" . $platformID
      )
      ->with("msg", $msg)->now();
    }
  }

  public function requirementState($platformID, $projectID, $versionID)
  {
    //Are there any actions at all?
    $actions = Action::collection()->loadWhere(
      [
      "platform_id" => $platformID,
      "version_id"  => $versionID
      ]
    );
    if($actions->count() == 0)
    {
      return VersionState::PENDING;
    }
    //Have there been any rejects?
    $rejects = Action::collection()->loadWhere(
      [
      "platform_id" => $platformID,
      "version_id"  => $versionID,
      "action_type" => ActionType::REJECT
      ]
    );
    if($rejects->count() > 0)
    {
      return VersionState::REJECTED;
    }
    //No, check approvals
    $approvals = ApprovalConfiguration::collection()->loadWhere(
      ["platform_id" => $platformID, "project_id" => $projectID]
    );
    foreach($approvals as $approval)
    {
      if(!$approval->required)
      {
        continue;
      }
      $approvalsGot    = $this->getApproverCount(
        $versionID,
        $platformID,
        $approval->role
      );
      $approvalsNeeded = $this->approversRequired(
        $projectID,
        $approval->role,
        $approval->consistency_level
      );
      if($approvalsGot < $approvalsNeeded)
      {
        return VersionState::REVIEW;
      }
    }
    return VersionState::APPROVED;
  }

  public function getApproverCount($versionID, $platformID, $userRole)
  {
    $approvers = Action::collection()->loadWhere(
      [
      "version_id"  => $versionID,
      "platform_id" => $platformID,
      "user_role"   => $userRole,
      "action_type" => ActionType::APPROVE
      ]
    );
    return $approvers->count();
  }

  public function approversRequired($projectID, $role, $consistency)
  {
    switch($consistency)
    {
      case Consistency::ONE:
        return 1;
      case Consistency::TWO:
        return 2;
      case Consistency::ALL:
        return $this->getTotalRoles($projectID, $role);
      case Consistency::QUORUM:
        return (floor($this->getTotalRoles($projectID, $role)) / 2) + 1;
      default:
        return 1;
    }
  }

  public function getTotalRoles($projectID, $role)
  {
    $count = 0;
    $users = ProjectUser::collection()->loadWhere(["project_id" => $projectID]);
    foreach($users as $user)
    {
      if(in_array($role, $user->roles))
      {
        $count++;
      }
    }
    return $count;
  }

  public function renderVersionRefresh()
  {
    $projectID  = $this->getInt("projectId");
    $versionID  = $this->getInt("versionId");
    $platformID = $this->getInt("platform");
    $state      = $this->requirementState($platformID, $projectID, $versionID);
    $pvs        = new PlatformVersionState();
    $pvs->hydrate(
      [
      "platform_id" => $platformID,
      "version_id"  => $versionID,
      "state"       => $state
      ]
    );
    $pvs->saveChanges();
    $msg       = new \stdClass();
    $msg->type = "success";
    $msg->text = "Status refreshed successfully";
    Redirect::to(
      $this->baseUri() .
      "/" . $projectID . "/v/" . $versionID . "/p/" . $platformID
    )
    ->with("msg", $msg)->now();
  }

  public function renderDeploy()
  {
    $projectId  = $this->getInt('projectId');
    $versionId  = $this->getInt('versionId');
    $platformId = $this->getInt('platform');
    $platform   = Platform::collection()->loadOneWhere(["id" => $platformId]);
    //Is it allowed?
    foreach($platform->requiredPlatforms as $required)
    {
      $pvs = PlatformVersionState::collection()->loadOneWhere(
        ["platform_id" => $required, "version_id" => $versionId]
      );
      if($pvs == null || $pvs->state != VersionState::APPROVED)
      {
        $msg       = new \stdClass();
        $msg->type = 'error';
        $msg->text = 'Version is not approved on a required previous platform';
        Redirect::to(
          $this->baseUri() . '/' . $projectId . '/v/' . $versionId
        )
        ->with('msg', $msg)->now();
      }
    }

    $deployRequest             = new \stdClass;
    $deployRequest->platformId = $platformId;
    $deployRequest->versionId  = $versionId;
    \Queue::push(new StdQueue('DeployRequest'), $deployRequest);

    /*$deployment = new Deployment();
    $deployment->hydrate(
      [
      "version_id"  => $versionId,
      "platform_id" => $platformId,
      "user_id"     => \Auth::user()->getId(),
      "project_id"  => $projectId,
      "deployed_on" => date("Y-m-d"),
      "comment"     => ""
      ]
    );
    $deployment->saveChanges();*/

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Version deployed successfully';
    Redirect::to(
      '/diffuse/' . $projectId . '/v/' . $versionId . '/p/' . $platformId
    )
    ->with('msg', $msg)->now();
  }

  public function getRoutes()
  {
    return [
      ':platform'         => 'platform',
      ':platform/refresh' => 'refresh',
      ':platform/deploy'  => 'deploy',
    ];
  }
}