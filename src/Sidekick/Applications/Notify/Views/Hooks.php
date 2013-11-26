<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Notify\Enums\NotifyType;
use Sidekick\Components\Notify\Mappers\EventHooks;
use Sidekick\Components\Notify\Mappers\NotifyGroup;
use Sidekick\Components\Users\Mappers\User;

class Hooks extends TemplatedViewModel
{

  public function getAllHooks()
  {
    return EventHooks::collection()->loadAll();
  }

  public function getMyHooks()
  {
  }

  public function getUser($username = null)
  {
    //Default to current user
    if($username == null)
    {
      $username = \Auth::user()->getUsername();
    }
    $user = User::collection()->loadOneWhere(["username" => $username]);
    return $user;
  }

  public function getNotifyType($id)
  {
    $nt = new NotifyType();
    return $nt->constFromValue($id);
  }

  public function getUserById($id)
  {
    $user = User::collection()->loadOneWhere(["id" => $id]);
    return ($user != null) ? $user->display_name : "";
  }

  public function getGroupById($id)
  {
    $group = NotifyGroup::collection()->loadOneWhere(["id" => $id]);
    return ($group != null) ? $group->groupName : "";
  }
}
