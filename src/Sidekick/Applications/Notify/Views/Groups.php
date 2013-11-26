<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Notify\Mappers\NotifyGroup;
use Sidekick\Components\Users\Mappers\User;

class Groups extends TemplatedViewModel
{
  public function getGroups()
  {
    return NotifyGroup::collection()->loadAll();
  }

  public function getUserFromId($id)
  {
    $user = User::collection()->loadOneWhere(["id" => $id]);
    return ($user != null) ? $user->username : "";
  }
}
