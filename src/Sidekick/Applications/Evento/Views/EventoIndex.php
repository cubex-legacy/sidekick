<?php
/**
 * Author: oke.ugwu
 * Date: 23/10/13 11:28
 */

namespace Sidekick\Applications\Evento\Views;

use Cubex\View\TemplatedViewModel;

class EventoIndex extends TemplatedViewModel
{
  protected $_openEvents;

  public function __construct($openEvents)
  {
    $this->_openEvents = $openEvents;
  }

  /**
   * @return \Sidekick\Components\Evento\Mappers\Event[]
   */
  public function openEvents()
  {
    return $this->_openEvents;
  }
}
