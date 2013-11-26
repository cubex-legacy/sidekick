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
use Sidekick\Applications\Notify\Views\Hooks;
use Sidekick\Applications\Notify\Views\HooksAddEdit;
use Sidekick\Components\Notify\Mappers\EventHooks;

class NotifyHooksController extends BaseControl
{

  public function renderIndex()
  {
    return $this->createView(new Hooks());
  }

  public function renderAdd()
  {
    return $this->createView(new HooksAddEdit("Add", null, null));
  }

  public function postAdd()
  {
    $key                = $this->_request->postVariables("eventKey");
    $types              = $this->_request->postVariables("notificationTypes");
    $users              = $this->_request->postVariables("users");
    $groups             = $this->_request->postVariables("groups");
    $hook               = new EventHooks();
    $hook->eventKey     = $key;
    $hook->notifyType   = $types;
    $hook->notifyUsers  = $users;
    $hook->notifyGroups = $groups;
    $hook->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'error';
    $msg->text = 'Notification request added successfully';
    Redirect::to("/notify/hooks")->with(
      'msg',
      $msg
    )->now();
  }

  public function renderEdit()
  {
    $id   = $this->getInt("hookid");
    $hook = EventHooks::collection()->loadOneWhere(["id" => $id]);
    if($hook == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'success';
      $msg->text = 'Hook does not exist';
      Redirect::to("/notify/hooks")->with(
        'msg',
        $msg
      )->now();
      return null;
    }
    return $this->createView(new HooksAddEdit("Edit", $id, $hook));
  }

  public function postEdit()
  {
    $id     = $this->_request->postVariables("id");
    $key    = $this->_request->postVariables("eventKey");
    $types  = $this->_request->postVariables("notificationTypes");
    $users  = $this->_request->postVariables("users");
    $groups = $this->_request->postVariables("groups");
    $hook   = EventHooks::collection()->loadOneWhere(["id" => $id]);
    if($hook == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Notification request does not exist';
      Redirect::to("/notify/hooks")->with(
        'msg',
        $msg
      )->now();
    }
    $hook->eventKey     = $key;
    $hook->notifyType   = $types;
    $hook->notifyUsers  = $users;
    $hook->notifyGroups = $groups;
    $hook->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'error';
    $msg->text = 'Notification request updated successfully';
    Redirect::to("/notify/hooks")->with(
      'msg',
      $msg
    )->now();
    return null;
  }

  public function renderDelete()
  {
    $id   = $this->getInt("hookid");
    $hook = EventHooks::collection()->loadOneWhere(["id" => $id]);
    if($hook == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Hook does not exist';
      Redirect::to("/notify/groups")->with(
        'msg',
        $msg
      )->now();
      return null;
    }
    $hook->delete();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Hook deleted successfully';
    Redirect::to("/notify/hooks")->with(
      'msg',
      $msg
    )->now();
    return null;
  }

  public function getSidebar()
  {
    $sidebarMenu = [
      "/notify"        => "Home",
      "/notify/events" => "Manage Event Types",
      "/notify/hooks"  => "My Notifications",
      "/notify/groups" => "Notification Groups"
    ];
    return new RenderGroup(
      new Sidebar($this->request()->path(2), $sidebarMenu)
    );
  }

  public function getRoutes()
  {
    return [
      '/add'     => 'add',
      '/:hookid' => [
        '/edit'   => 'edit',
        '/delete' => 'delete'
      ]
    ];
  }
}
