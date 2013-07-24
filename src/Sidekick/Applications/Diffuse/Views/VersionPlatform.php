<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 23/07/13
 * Time: 10:59
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Form\Form;
use Cubex\View\HtmlElement;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Sidekick\Enums\Consistency;

class VersionPlatform extends TemplatedViewModel
{
  protected $_nav;
  protected $_platformID;
  protected $_projectID;
  protected $_versionID;
  protected $_platform;
  protected $_state;

  public function __construct($platformID, $projectID, $versionID, $nav)
  {
    $this->_platformID = $platformID;
    $this->_projectID  = $projectID;
    $this->_versionID  = $versionID;
    $this->_nav        = $nav;
    $this->_platform   = Platform::collection()->loadOneWhere(
      ["id" => $this->_platformID]
    );
    $myState           = PlatformVersionState::collection()->loadOneWhere(
      [
      "platform_id" => $platformID,
      "version_id"  => $versionID
      ]
    );
    $this->_state      = ($myState == null) ? VersionState::UNKNOWN : $myState->state;
  }

  public function meetsApprovalRequirements()
  {
    $pvs = PlatformVersionState::collection()->loadOneWhere(
      ["platform_id" => $this->_platformID, "version_id" => $this->_versionID]
    );
    if($pvs == null || $pvs->state != VersionState::APPROVED)
    {
      return false;
    }
    else
    {
      return true;
    }
  }

  public function getApprovalConfiguration()
  {
    $approvals = ApprovalConfiguration::collection()->loadWhere(
      ["platform_id" => $this->_platformID, "project_id" => $this->_projectID]
    );
    return $approvals;
  }

  public function getActionHistory()
  {
    $actions = Action::collection()->loadWhere(
                 [
                 "platform_id" => $this->_platformID,
                 "version_id"  => $this->_versionID
                 ]
               )->setOrderBy("updated_at", "DESC");
    return $actions;
  }

  public function getActionForm()
  {
    $form    = new Form("ActionForm");
    $myRoles = $this->getMyRoles(true);
    if($myRoles !== "")
    {
      $form->addSelectElement("role", $myRoles);
      $availableActions = [
        "comment" => "Comment",
        "approve" => "Approve",
        "reject"  => "Reject"
      ];
    }
    else
    {
      $availableActions = ["comment" => "Comment"];
    }
    $form->addSelectElement(
      "Action",
      $availableActions
    );
    $form->addTextareaElement("comment");
    $form->addSubmitElement("Perform Action");
    if($myRoles !== "")
    {
      $form->getElement("role")->setLabel("Action as:");
    }
    return $form;
  }

  public function getDeployForm()
  {
    if($this->meetsApprovalRequirements())
    {
      $container = new HTMLElement("p");
      $container->nest(
        new HTMLElement("a", [
                             "class" => "btn btn-primary",
                             "href"  => "/diffuse/" . $this->_projectID . "/" . $this->_versionID . "/" . $this->_platformID . "/deploy"
                             ], "Deploy to " . $this->_platform->name)
      );
      return $container;
    }
    else
    {
      return new HTMLElement("p", [], "This version cannot currently be deployed");
    }
  }

  public function getMyRoles($assoc = false)
  {
    $role = ProjectUser::collection()->loadOneWhere(
      ["project_id" => $this->_projectID, "user_id" => \Auth::user()->getId()]
    );
    if($role === null)
    {
      return "";
    }
    else
    {
      //Roles are stored as [developer, manager]
      //If assoc==true, an associative array ["developer"=>"developer"], to be used in select elements, is returned
      if($assoc)
      {
        $assocArray = [];
        foreach($role->roles as $r)
        {
          $assocArray[$r] = $r;
        }
        return $assocArray;
      }
      else
      {
        return $role->roles;
      }
    }
  }

  public function getNav()
  {
    return $this->_nav;
  }

  public function getApproverCount($versionID, $userRole)
  {
    $approvers = Action::collection()->loadWhere(
      [
      "version_id"  => $versionID,
      "user_role"   => $userRole,
      "platform_id" => $this->_platformID,
      "action_type" => ActionType::APPROVE
      ]
    );
    return count($approvers);
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
}