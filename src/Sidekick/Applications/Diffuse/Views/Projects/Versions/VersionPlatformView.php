<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Versions;

use Cubex\Helpers\Strings;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\HtmlElement;
use Cubex\View\Impart;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Qubes\Bootstrap\Icon;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Sidekick\Enums\Consistency;
use Sidekick\Components\Users\Mappers\User;

class VersionPlatformView extends TemplatedViewModel
{
  protected $_platformId;
  protected $_platform;
  /**
   * @var Action[]|RecordCollection
   */
  protected $_actions;
  /**
   * @var RecordCollection|ApprovalConfiguration[]
   */
  protected $_approvals;
  /**
   * @var RecordCollection|ProjectUser[]
   */
  protected $_projectUsers;
  /**
   * @var Platform[]|RecordCollection
   */
  protected $_requiredPlatforms = [];

  public function __construct(
    Platform $platform, $actions, $deployments, $approvals
  )
  {
    $this->_platformId  = $platform->id();
    $this->_platform    = $platform;
    $this->_actions     = $actions;
    $this->_deployments = $deployments;
    $this->_approvals   = $approvals;
  }

  public function setProjectUsers($users)
  {
    $this->_projectUsers = $users;
    return $this;
  }

  /**
   * @return Action[]
   */
  public function getActions()
  {
    return $this->_actions;
  }

  /**
   * @return Deployment[]
   */
  public function getDeployments()
  {
    return $this->_deployments;
  }

  /**
   * @return ApprovalConfiguration[]
   */
  public function getApprovalConfigs()
  {
    return $this->_approvals;
  }

  public function getApprovalBoxes()
  {
    $boxes     = new RenderGroup();
    $approvers = [];
    $rejecters = [];
    $users     = [];

    foreach($this->_projectUsers as $user)
    {
      foreach($user->roles as $role)
      {
        $users[$role] = $user->id();
      }
    }

    foreach($this->_actions as $action)
    {
      if($action->actionType === ActionType::APPROVE)
      {
        $approvers[$action->userRole][] = $action->userId;
      }
      else if($action->actionType === ActionType::REJECT)
      {
        $rejecters[$action->userRole][] = $action->userId;
      }
    }

    foreach($this->_approvals as $approval)
    {
      if(!isset($users[$approval->role]))
      {
        $users[$approval->role] = [];
      }
      if(!isset($rejecters[$approval->role]))
      {
        $rejecters[$approval->role] = [];
      }
      if(!isset($approvers[$approval->role]))
      {
        $approvers[$approval->role] = [];
      }

      $passed        = $pending = $message = false;
      $totalRequired = 0;
      switch($approval->consistencyLevel)
      {
        case Consistency::ONE:
          $totalRequired = 1;
          break;
        case Consistency::TWO:
          $totalRequired = 2;
          break;
        case Consistency::ALL:
          $totalRequired = count($users[$approval->role]);
          break;
        case Consistency::QUORUM:
          $totalRequired = ceil(count($users[$approval->role]) / 2) + 1;
          break;
      }
      $pending = $totalRequired - count($approvers[$approval->role]);

      if($pending < 1)
      {
        $status = new Icon(Icon::ICON_OK);
        $class  = 'alert-success';
      }
      else if($pending && count($approvers[$approval->role]) > 0)
      {
        $status = 'Waiting for ' . $this->tp("%d other(s)", $pending);
        $class  = 'alert-success';
      }
      else
      {
        $message = $this->tp("%d required", $totalRequired);
        $status  = 'Awaiting Approval';
        $class   = 'alert-error';
      }

      if(!$passed && !$approval->required)
      {
        $class = 'alert-info';
      }

      $approvalBox = new HtmlElement('div', ['class' => 'alert ' . $class]);
      $approvalBox->nestElement(
        'strong',
        [],
        (Strings::titleize($approval->role) . " Approval: ")
      );
      $approvalBox->nest(
        new Impart($message ? : implode_list(
          User::collection()->loadIds($approvers[$approval->role])
          ->getUniqueField("displayName")
        ))
      );
      $approvalBox->nestElement("span", ['class' => 'pull-right'], $status);
      $boxes->add($approvalBox);
    }

    return $boxes;
  }

  public function requiredPlatforms()
  {
    return $this->_requiredPlatforms;
  }

  public function setRequiredPlatforms($requiredPlatforms)
  {
    $this->_requiredPlatforms = $requiredPlatforms;
    return $this;
  }
}
