<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento\Controllers;

use Cubex\Routing\Templates\ResourceTemplate;
use Sidekick\Applications\Evento\Views\EventoSidebar;
use Sidekick\Applications\Evento\Views\EventoTypeForm;
use Sidekick\Applications\Evento\Views\EventoTypesIndex;
use Sidekick\Applications\Evento\Views\EventView;
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

  public function renderShow()
  {
    $eventId = $this->getInt('id');
    $event   = new Event($eventId);
    return new EventView($event);
  }

  public function renderCreate()
  {
    $postData = $this->request()->postVariables();

    $eventType = new EventType();
    $eventType->hydrate($postData);
    $eventType->saveChanges();

    \Redirect::to($this->baseUri())->now();
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
