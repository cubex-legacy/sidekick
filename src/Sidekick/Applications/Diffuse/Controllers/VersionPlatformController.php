<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 24/07/13
 * Time: 10:05
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Facade\Redirect;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Applications\Diffuse\Views\VersionPlatform;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Sidekick\Enums\Consistency;

class VersionPlatformController extends DiffuseController
{

  public function renderIndex()
  {
    Redirect::to("/diffuse")->now();
  }

  public function renderVersionPlatform()
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

  public function postVersionPlatform()
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
          $this->baseUri(
          ) . "/" . $projectID . "/" . $versionID . "/" . $platformID
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
        $this->baseUri(
        ) . "/" . $projectID . "/" . $versionID . "/" . $platformID
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
        $this->baseUri(
        ) . "/" . $projectID . "/" . $versionID . "/" . $platformID
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
      $this->baseUri() . "/" . $projectID . "/" . $versionID . "/" . $platformID
    )
    ->with("msg", $msg)->now();
  }

  public function getNav($page = "")
  {
    $project = $this->getInt("projectId");
    $version = $this->getInt("versionId");
    $active  = ["class" => "active"];
    $list    = new HTMLElement("ul", ["class" => "nav nav-tabs"]);
    $list->nestElement(
      "li",
      ($page == "") ? $active : [],
      "<a href='/diffuse/$project/$version/'>Version Details</a>"
    );
    $list->nestElement(
      "li",
      ($page == "changelog") ? $active : [],
      "<a href='/diffuse/$project/$version/changelog'>Change Log</a>"
    );
    $platforms = Platform::collection()->loadAll();
    foreach($platforms as $platform)
    {
      $list->nestElement(
        "li",
        ($page == $platform->name) ? $active : [],
        "<a href='/diffuse/platform/$project/$version/" . $platform->id . "'>" . $platform->name . "</a>"
      );
    }
    return $list;
  }

  public function getRoutes()
  {
    return [
      '/:projectId/:versionId@num/:platform'         => 'versionPlatform',
      '/:projectId/:versionId@num/:platform/refresh' => 'versionRefresh'
    ];
  }
}
