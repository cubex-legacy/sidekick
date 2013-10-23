<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento\Controllers;

use Cubex\Routing\Templates\ResourceTemplate;
use Sidekick\Applications\Evento\Views\EventoForm;
use Sidekick\Applications\Evento\Views\EventoIndex;
use Sidekick\Applications\Evento\Views\EventoSidebar;
use Sidekick\Applications\Evento\Views\EventoView;
use Sidekick\Components\Evento\Enums\Resolution;
use Sidekick\Components\Evento\Mappers\Event;
use Sidekick\Components\Evento\Mappers\EventUpdate;

class EventoSummaryController extends EventoController
{
  public function preRender()
  {
    parent::preRender();
    $this->nest('sidebar', new EventoSidebar());
  }

  public function renderIndex()
  {
    $openEvents = Event::collection()->whereEq('closedAt', null)->setOrderBy(
      'openedAt',
      'DESC'
    );
    return new EventoIndex($openEvents);
  }

  public function renderNew()
  {
    return new EventoForm(new Event());
  }

  public function renderShow()
  {
    $eventId = $this->getInt('id');
    $event   = new Event($eventId);

    $updates = EventUpdate::collection(['event_id' => $eventId]);

    return new EventoView($event, $updates);
  }

  public function renderCreate()
  {
    $postData = $this->request()->postVariables();

    $event = new Event();
    $event->hydrate($postData);
    $event->openedAt = date('Y-m-d H:i:s');
    $event->owner    = \Auth::user()->getId();
    $event->saveChanges();

    \Redirect::to($this->baseUri())->now();
  }

  public function renderUpdate()
  {
    $eventUpdate              = new EventUpdate();
    $eventUpdate->eventId     = $this->getInt('id');
    $eventUpdate->userId      = \Auth::user()->getId();
    $eventUpdate->resolution = $this->request()->postVariables('resolution');
    $eventUpdate->description = $this->request()->postVariables('description');
    $eventUpdate->saveChanges();

    if($eventUpdate->resolution == Resolution::CLOSED)
    {
      $event = new Event($eventUpdate->eventId);
      $event->closedAt = date('Y-m-d H:i:s');
      $event->saveChanges();
    }

    \Redirect::to($this->baseUri() . '/' . $this->getInt('id'))->now();
  }

  public function getRoutes()
  {
    $routes = ResourceTemplate::getRoutes();
    return $routes;
  }
}
