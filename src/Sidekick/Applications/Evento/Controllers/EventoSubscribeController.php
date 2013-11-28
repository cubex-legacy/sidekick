<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Routing\StdRoute;
use Cubex\Routing\Templates\ResourceTemplate;
use Sidekick\Applications\Evento\Views\EventoSubscribeIndex;
use Sidekick\Components\Enums\Severity;
use Sidekick\Components\Evento\Mappers\EventSubscription;
use Sidekick\Components\Evento\Mappers\EventType;

class EventoSubscribeController extends EventoController
{
  public function renderIndex()
  {
    $eventTypes         = EventType::collection();
    $eventSubscriptions = EventSubscription::collection(
      ['user_id' => \Auth::user()->getId()]
    );
    return new EventoSubscribeIndex($eventTypes, $eventSubscriptions);
  }

  public function renderShow()
  {
    $this->_subscribe();
    Redirect::to($this->baseUri())->now();
  }

  public function ajaxCreate()
  {
    $this->_subscribe();
    exit(1);
  }

  private function _subscribe()
  {
    //subscribe users
    $eventTypeId = $this->getInt(
      'id',
      $this->request()->postVariables('eventTypeId')
    );
    $userId      = \Auth::user()->getId();

    $eventSub           = new EventSubscription([$eventTypeId, $userId]);
    $eventSub->severity = $this->request()->postVariables(
      'severity',
      Severity::LOW
    );
    $eventSub->saveChanges();
  }

  public function renderUnsubscribe()
  {
    //subscribe users
    $eventTypeId = $this->getInt('id');
    $userId      = \Auth::user()->getId();

    $eventSub = new EventSubscription([$eventTypeId, $userId]);
    $eventSub->delete();

    Redirect::to($this->baseUri())->now();
  }

  public function getRoutes()
  {
    $routes = ResourceTemplate::getRoutes();
    array_unshift($routes, new StdRoute('/:id/unsubscribe', 'unsubscribe'));
    return $routes;
  }
}
