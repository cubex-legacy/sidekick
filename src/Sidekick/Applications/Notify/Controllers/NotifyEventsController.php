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
use Sidekick\Applications\Notify\Views\Events;
use Sidekick\Applications\Notify\Views\EventsAddEdit;
use Sidekick\Components\Notify\Mappers\EventTypes;

class NotifyEventsController extends BaseControl
{

  public function renderIndex()
  {
    return $this->createView(new Events());
  }

  public function renderAdd()
  {
    return $this->createView(new EventsAddEdit("Add", null, null));
  }

  public function postAdd()
  {
    $key = $this->_request->postVariables("eventKey");
    $desc = $this->_request->postVariables("eventDescription");
    $apps = $this->_request->postVariables("eventApplications");
    $type = $this->_request->postVariables("eventType");
    $params = $this->_request->postVariables("parameter");
    $event = new EventTypes();
    $event->eventKey = $key;
    $event->eventDescription = $desc;
    $event->eventApplications = $apps;
    $event->eventType = $type;
    $event->eventParams = $params;
    $event->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Event type successfully added';
    Redirect::to("/notify/events")->with(
      'msg',
      $msg
    )->now();
  }

  public function renderEdit()
  {
    $id = $this->getInt("eventid");
    $event = EventTypes::collection()->loadOneWhere([
      "id" => $id
    ]);
    if($event == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Event does not exist';
      Redirect::to("/notify/events")->with(
        'msg',
        $msg
      )->now();
      return null;
    }
    return $this->createView(new EventsAddEdit("Edit", $id, $event));
  }

  public function postEdit()
  {
    $id = $this->_request->postVariables("id");
    $key = $this->_request->postVariables("eventKey");
    $desc = $this->_request->postVariables("eventDescription");
    $apps = $this->_request->postVariables("eventApplications");
    $type = $this->_request->postVariables("eventType");
    $params = $this->_request->postVariables("parameter");
    $event = EventTypes::collection()->loadOneWhere([
      "id" => $id
    ]);
    if($event == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Event does not exist';
      Redirect::to("/notify/events")->with(
        'msg',
        $msg
      )->now();
    }
    $event->eventKey = $key;
    $event->eventDescription = $desc;
    $event->eventApplications = $apps;
    $event->eventType = $type;
    $event->eventParams = $params;
    $event->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Event type successfully updated';
    Redirect::to("/notify/events")->with(
      'msg',
      $msg
    )->now();
  }

  public function renderDelete()
  {
    $id = $this->getInt("eventid");
    $event = EventTypes::collection()->loadOneWhere([
      "id" => $id
    ]);
    if($event == null)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Event does not exist';
      Redirect::to("/notify/events")->with(
        'msg',
        $msg
      )->now();
    }
    $event->delete();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Event deleted successfully';
    Redirect::to("/notify/events")->with(
      'msg',
      $msg
    )->now();
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
      '/add' => 'add',
      '/:eventid' => [
        '/edit'   => 'edit',
        '/delete' => 'delete'
      ]
    ];
  }
}
