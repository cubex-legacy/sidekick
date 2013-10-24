<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Controllers;


use Cubex\Facade\Redirect;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Notify\Views\Groups;
use Sidekick\Applications\Notify\Views\GroupsAddEdit;
use Sidekick\Components\Notify\Mappers\NotifyGroup;

class NotifyGroupsController extends BaseControl
{

  public function renderIndex()
  {
    return $this->createView(new Groups());
  }

  public function renderAdd()
  {
    return $this->createView(new GroupsAddEdit("Add", null,  null));
  }

  public function postAdd()
  {
    $group = new NotifyGroup();
    $group->hydrate($this->_request->postVariables());
    $group->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Group successfully added';
    Redirect::to("/notify/groups")->with(
      'msg',
      $msg
    )->now();
  }

  public function renderEdit()
  {
    $id = $this->getInt("groupid");
    $group = NotifyGroup::collection()->loadOneWhere([
      "id" => $id
    ]);
    if($group == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Group does not exist';
      Redirect::to("/notify/groups")->with(
        'msg',
        $msg
      )->now();
      return null;
    }
    return $this->createView(new GroupsAddEdit("Edit", $id, $group));
  }

  public function postEdit()
  {
    $id = $this->_request->postVariables("id");
    $name = $this->_request->postVariables("groupName");
    $users = $this->_request->postVariables("groupUsers");
    $group = NotifyGroup::collection()->loadOneWhere([
      "id" => $id
    ]);
    if($group == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Group does not exist';
      Redirect::to("/notify/groups")->with(
        'msg',
        $msg
      )->now();
      return null;
    }
    $group->groupName = $name;
    $group->groupUsers = $users;
    $group->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Group updated successfully';
    Redirect::to("/notify/groups")->with(
      'msg',
      $msg
    )->now();
    return null;
  }

  public function renderDelete()
  {
    $id = $this->getInt("groupid");
    $group = NotifyGroup::collection()->loadOneWhere([
      "id" => $id
    ]);
    if($group == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Group does not exist';
      Redirect::to("/notify/groups")->with(
        'msg',
        $msg
      )->now();
      return null;
    }
    $group->delete();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Group deleted successfully';
    Redirect::to("/notify/groups")->with(
      'msg',
      $msg
    )->now();
    return null;
  }

  public function getSidebar()
  {
    $sidebarMenu = [
      "/notify" => "Home",
      "/notify/events" => "Manage Event Types",
      "/notify/hooks" => "My Notifications",
      "/notify/groups" => "Notification Groups"
    ];
    return new RenderGroup(
      new Sidebar($this->request()->path(2), $sidebarMenu)
    );
  }

  public function getRoutes()
  {
    return [
      '/add'  =>  'add',
      '/:groupid'  => [
        '/edit' => 'edit',
        '/delete' => 'delete'
      ]
    ];
  }
}
