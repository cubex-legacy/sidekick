<?php
/**
 * Author: oke.ugwu
 * Date: 26/11/13 12:03
 */

namespace Sidekick\Components\Notify;

use Sidekick\Components\Notify\Filters\AbstractFilter;

class NotifyConfigItem
{
  public $eventKey;
  public $eventName;
  public $eventDescription;
  public $filters = [];

  public function __construct($eventKey, $eventName, $eventDescription)
  {
    $this->eventKey         = $eventKey;
    $this->eventName        = $eventName;
    $this->eventDescription = $eventDescription;
  }

  /**
   * @param mixed $eventName
   */
  public function setEventName($eventName)
  {
    $this->eventName = $eventName;
  }

  /**
   * @return mixed
   */
  public function getEventName()
  {
    return $this->eventName;
  }

  /**
   * @param string $eventDescription
   */
  public function setEventDescription($eventDescription)
  {
    $this->eventDescription = $eventDescription;
  }

  /**
   * @return string
   */
  public function getEventDescription()
  {
    return $this->eventDescription;
  }

  /**
   * @param AbstractFilter $filter
   */
  public function addFilter($filter)
  {
    $this->filters[$filter->getName()] = $filter;
  }

  /**
   * @param string $name
   *
   * @return AbstractFilter $filter
   */
  public function getFilter($name)
  {
    return $this->filters[$name];
  }

  /**
   * @return AbstractFilter[]
   */
  public function getFilters()
  {
    return $this->filters;
  }
}
