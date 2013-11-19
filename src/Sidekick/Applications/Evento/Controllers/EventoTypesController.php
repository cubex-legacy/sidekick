<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento\Controllers;

use Cubex\Routing\Templates\ResourceTemplate;
use Sidekick\Applications\Evento\Views\EventoSidebar;
use Sidekick\Applications\Evento\Views\EventoTypeForm;
use Sidekick\Applications\Evento\Views\EventoTypesIndex;
use Sidekick\Components\Evento\Mappers\Event;
use Sidekick\Components\Evento\Mappers\EventType;

class EventoTypesController extends EventoController
{
  public function preRender()
  {
    parent::preRender();
    $this->nest('sidebar', new EventoSidebar());
  }

  public function renderIndex()
  {
    $eventTypes = EventType::collection();
    return new EventoTypesIndex($eventTypes);
  }

  public function renderNew()
  {
    return new EventoTypeForm(new EventType());
  }

  public function renderEdit()
  {
    $eventTypeId = $this->getInt('id');
    return new EventoTypeForm(new EventType($eventTypeId));
  }

  public function renderUpdate()
  {
    $postData = $this->request()->postVariables();

    $eventType = new EventType($postData['id']);
    $eventType->hydrate($postData);
    $eventType->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Event Type was successfully updated';

    \Redirect::to($this->baseUri())->with('msg', $msg)->now();
    \Redirect::to($this->baseUri())->now();
  }

  public function renderCreate()
  {
    $postData = $this->request()->postVariables();

    $eventType = new EventType();
    $eventType->hydrate($postData);
    $eventType->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Event Type was successfully created';

    \Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function renderDestroy()
  {
    $eventTypeId = $this->getInt('id');

    $events = Event::collection(['eventTypeId' => $eventTypeId]);
    if(!$events->hasMappers())
    {
      $eventType = new EventType($eventTypeId);
      $eventType->delete();

      $msg       = new \stdClass();
      $msg->type = 'success';
      $msg->text = 'Event Type was successfully deleted';
    }
    else
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Event Type could not be deleted,' .
        ' because events of this type already exists';
    }

    \Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}