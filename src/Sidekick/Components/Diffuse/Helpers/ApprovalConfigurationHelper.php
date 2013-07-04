<?php
/**
 * Author: oke.ugwu
 * Date: 04/07/13 10:08
 */

namespace Sidekick\Components\Diffuse\Helpers;

use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Sidekick\Enums\Consistency;

class ApprovalConfigurationHelper
{
  public static function getTotalUsersCount($projectId)
  {
    $result = [];
    $users  = ProjectUser::collection(['project_id' => $projectId])->load();
    foreach($users as $p)
    {
      foreach($p->roles as $role)
      {
        if(!isset($result[$role]))
        {
          $result[$role] = 0;
        }
        $result[$role] += 1;
      }
    }

    return $result;
  }

  /**
   * @param $actions
   * @param $projectId
   *
   * @return array - An array of count of approve actions group by role
   */
  public static function getApproveActionCount($actions, $projectId)
  {
    $actionCount = [];
    foreach($actions as $a)
    {
      $user = new ProjectUser([$projectId, $a->userId]);
      foreach($user->roles as $role)
      {
        if(!isset($actionCount[$role]))
        {
          $actionCount[$role] = 0;
        }

        //only count the number of approves for each role
        //and only count if user is member of this project
        if($a->actionType == ActionType::APPROVE && $user->exists())
        {
          $actionCount[$role] += 1;
        }
      }
    }
    return $actionCount;
  }

  public static function getConsistencyLevelInt($consistencyLevel, $total)
  {
    switch($consistencyLevel)
    {
      case Consistency::NONE:
        $int = 0;
        break;
      case Consistency::ONE:
        $int = 1;
        break;
      case Consistency::TWO:
        $int = 2;
        break;
      case Consistency::ALL:
        $int = $total;
        break;
      case Consistency::QUORUM:
        $int = floor($total / 2) + 1;
        break;
      default:
        $int = $total;
    }

    return $int;
  }

  /**
   * Tries to figure out if actions taken on a version is sufficient to
   * automatically approve it.
   */
  public static function isAutoApproveReady($actions, $projectId)
  {
    $total    = ApprovalConfigurationHelper::getTotalUsersCount($projectId);
    $actionLk = ApprovalConfigurationHelper::getApproveActionCount(
      $actions,
      $projectId
    );

    /**
     * @var $config ApprovalConfiguration[]
     */
    $config = ApprovalConfiguration::collection(['project_id' => $projectId])
              ->load()->setOrderBy('required', 'DESC');

    $autoApprove = false;
    foreach($config as $c)
    {
      if(!isset($actionLk[$c->role]))
      {
        $actionLk[$c->role] = 0;
      }

      $roleConsistency = ApprovalConfigurationHelper::getConsistencyLevelInt(
        $c->consistencyLevel,
        $total[$c->role]
      );

      $role = $c->role;
      if($c->required)
      {
        if($actionLk[$role] >= $roleConsistency)
        {
          $autoApprove = true;
        }
        else
        {
          $autoApprove = false;
          //no point going further.
          //One of the config requirement has not been met
          break;
        }
      }
      else
      {
        if($actionLk[$role] >= $roleConsistency)
        {
          $autoApprove = true;
        }
      }
    }

    return $autoApprove;
  }
}
