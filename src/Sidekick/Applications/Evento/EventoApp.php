<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento;

use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Applications\Evento\Controllers\EventoSummaryController;
use Sidekick\Components\Enums\Severity;
use Sidekick\Components\Notify\Interfaces\INotifiableApp;
use Sidekick\Components\Notify\NotifyConfig;
use Sidekick\Components\Notify\NotifyConfigItem;

class EventoApp extends SidekickApplication implements INotifiableApp
{
  public function getNavGroup()
  {
    return "Evento";
  }

  public function getBundles()
  {
    return [
    ];
  }

  public function name()
  {
    return "Evento";
  }

  public function description()
  {
    return "System Events";
  }

  public function defaultController()
  {
    return new EventoSummaryController();
  }

  public function userPermitted($userRole)
  {
    return true;
  }

  /**
   * @return NotifyConfig
   */
  public function getNotifyConfig()
  {
    $severityOptions = array_flip((new Severity())->getConstList());
    $c1              = new NotifyConfigItem(
      'event.create', 'Event Create', 'When Evento event is created/opened'
    );
    $c1->setEventTypes(['Marketing', 'Server Downtime', 'Bug Found']);
    $c1->addFilter('Severity', $severityOptions);

    $c3 = new NotifyConfigItem(
      'event.update', 'Event Update', 'When Evento event is updated'
    );
    $c3->setEventTypes(['Marketing', 'Server Downtime']);
    $c3->addFilter('Severity', $severityOptions);

    $c4 = new NotifyConfigItem(
      'event.close', 'Event Close', 'When Evento event is closed/resolved'
    );
    $c4->setEventTypes(['Marketing', 'Server Downtime']);
    $c4->addFilter('Severity', $severityOptions);

    $nc = new NotifyConfig();
    $nc->addItem($c1);
    $nc->addItem($c3);
    $nc->addItem($c4);

    return $nc;
  }

  public function getRoutes()
  {
    return [
      '/'               => 'EventoSummaryController',
      '/types/(.*)'     => 'EventoTypesController',
      '/subscribe/(.*)' => 'EventoSubscribeController',
    ];
  }
}
