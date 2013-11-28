<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Evento\Views\EventoSidebar;

abstract class EventoController extends BaseControl
{
  public function preRender()
  {
    parent::preRender();
    $this->nest('sidebar', new EventoSidebar($this->appBaseUri()));
  }
}
