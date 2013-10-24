<?php
/**
 * Author: oke.ugwu
 * Date: 23/10/13 11:28
 */

namespace Sidekick\Applications\Evento\Views;

use Cubex\View\TemplatedViewModel;

class EventoIndex extends TemplatedViewModel
{
  protected $_events;
  protected $_title;

  public function __construct($title, $openEvents)
  {
    $this->_title  = $title;
    $this->_events = $openEvents;
  }

  public function title()
  {
    return $this->_title;
  }

  /**
   * @return \Sidekick\Components\Evento\Mappers\Event[]
   */
  public function events()
  {
    return $this->_events;
  }
}
