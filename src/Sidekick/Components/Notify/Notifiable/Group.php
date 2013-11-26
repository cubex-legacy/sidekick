<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Notifiable;

use Sidekick\Components\Notify\Mappers\NotifyGroup;
use Sidekick\Components\Users\Mappers\User;
use Sidekick\Components\Notify\INotifiable;

class Group implements INotifiable
{
  protected $_groups;
  protected $_users;

  public function __construct($ids)
  {
    $this->_groups = [];
    $this->_users  = [];
    //Add all groups
    if(!is_array($ids))
    {
      array_push($this->_groups, $this->_idToGroup($ids));
    }
    else
    {
      foreach($ids as $id)
      {
        array_push($this->_groups, $this->_idToGroup($id));
      }
    }
    //Get all users from all groups
    foreach($this->_groups as $group)
    {
      if(!is_array($group->groupUsers))
      {
        array_push($this->_users, $this->_idToUser($group->groupUsers));
      }
      else
      {
        foreach($group->groupUsers as $user)
        {
          array_push($this->_users, $this->_idToUser($user));
        }
      }
    }
  }

  public function contains($user)
  {
  }

  public function getNotifiableUsers()
  {
  }

  protected function _idToGroup($id)
  {
    $group = NotifyGroup::collection()->loadOneWhere(
      [
      "id" => $id
      ]
    );
    return $group;
  }

  protected function _idToUser($id)
  {
    $user = User::collection()->loadOneWhere(
      [
      "id" => $id
      ]
    );
    return $user;
  }
}
