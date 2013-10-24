<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Notifiable;

use Sidekick\Components\Notify\INotifiable;

class User implements INotifiable
{
  protected $_users;

  public function __construct($ids)
  {
    $this->_users = [];
    if(!is_array($ids))
    {
      array_push($this->_users, $this->idToUser($ids));
    }
    else
    {
      foreach($ids as $id)
      {
        array_push($this->_users, $this->idToUser($id));
      }
    }
  }

  public function contains($user)
  {
    foreach($this->_users as $user)
    {
      if($user->id == $user || $user->username == $user)
      {
        return true;
      }
    }
    return false;
  }

  public function getNotifiableUsers()
  {

  }

  private function idToUser($id)
  {
    $user = \Sidekick\Components\Users\Mappers\User::collection()->loadOneWhere([
      "id" => $id
    ]);
    return $user;
  }
}
