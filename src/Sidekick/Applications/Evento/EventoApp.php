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
    return "Events";
  }

  public function getBundles()
  {
    return [
    ];
  }

  public function name()
  {
    return "Eventos";
  }

  public function description()
  {
    return "System Events";
  }

  public function defaultController()
  {
    return new EventoSummaryController();
  }
}
