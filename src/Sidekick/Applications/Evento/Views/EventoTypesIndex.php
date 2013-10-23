<?php
/**
 * Author: oke.ugwu
 * Date: 23/10/13 11:28
 */

namespace Sidekick\Applications\Evento\Views;

use Cubex\View\TemplatedViewModel;

class EventoTypesIndex extends TemplatedViewModel
{
  protected $_eventTypes;

  public function __construct($eventTypes)
  {
    $this->_eventTypes = $eventTypes;
  }

  /**
   * @return \Sidekick\Components\Evento\Mappers\EventType[]
   */
  public function eventTypes()
  {
    return $this->_eventTypes;
  }
}
