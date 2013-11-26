<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento;

use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Evento\Controllers\EventoSummaryController;

class EventoApp extends BaseApp
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

  public function getRoutes()
  {
    return [
      '/'               => 'EventoSummaryController',
      '/types/(.*)'     => 'EventoTypesController',
      '/subscribe/(.*)' => 'EventoSubscribeController',
    ];
  }
}
