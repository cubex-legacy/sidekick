<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento\Controllers;

use Sidekick\Applications\Evento\Views\EventView;

class EventoSummaryController extends EventoController
{
  public function renderIndex()
  {
    return new EventView();
  }
}
