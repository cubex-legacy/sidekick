<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Helpers;

use Cubex\Mapper\Database\RecordCollection;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Sidekick\Enums\Consistency;

class VersionApproval
{
  /**
   * @param $projectUsers  RecordCollection|ProjectUser[]
   * @param $actions       RecordCollection|Action[]
   * @param $approvalRules RecordCollection|ApprovalConfiguration[]
   *
   * @return array
   */
  public static function status($projectUsers, $actions, $approvalRules)
  {
    $return = $users = $rejectors = $approvers = [];
    foreach($projectUsers as $user)
    {
      foreach($user->roles as $role)
      {
        $users[$role][] = $user->userId;
      }
    }

    foreach($actions as $action)
    {
      if($action->actionType === ActionType::APPROVE)
      {
        $approvers[$action->userRole][] = $action->userId;
      }
      else if($action->actionType === ActionType::REJECT)
      {
        $rejectors[$action->userRole][] = $action->userId;
      }
    }

    foreach($approvalRules as $approval)
    {
      $users[$approval->role]     = array_unique(
        isset($users[$approval->role]) ? $users[$approval->role] : []
      );
      $rejectors[$approval->role] = array_unique(
        isset($rejectors[$approval->role]) ? $rejectors[$approval->role] : []
      );
      $approvers[$approval->role] = array_unique(
        isset($approvers[$approval->role]) ? $approvers[$approval->role] : []
      );

      $approvalData = [
        'pending'      => 0,
        'required'     => 0,
        'waiting'      => 0,
        'require_pass' => $approval->required,
        'users'        => $users[$approval->role],
        'approvers'    => $approvers[$approval->role],
        'rejectors'    => $rejectors[$approval->role]
      ];

      $approvalData['required'] = 0;
      switch($approval->consistencyLevel)
      {
        case Consistency::ONE:
          $approvalData['required'] = 1;
          break;
        case Consistency::TWO:
          $approvalData['required'] = 2;
          break;
        case Consistency::ALL:
          $approvalData['required'] = count($users[$approval->role]);
          break;
        case Consistency::QUORUM:
          $approvalData['required'] = ceil(
            count($users[$approval->role]) / 2
          ) + 1;
          break;
      }
      $approvalData['pending'] = $approvalData['required'] - count(
        $approvers[$approval->role]
      );

      $key = implode(
        '-',
        [$approval->projectId, $approval->platformId, $approval->role]
      );

      $approvalData['project_id']  = $approval->projectId;
      $approvalData['platform_id'] = $approval->platformId;
      $approvalData['role']        = $approval->role;

      $return[$key] = $approvalData;
    }
    return $return;
  }
}
