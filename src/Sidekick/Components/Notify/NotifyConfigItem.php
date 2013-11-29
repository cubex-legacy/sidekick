<?php
/**
 * Author: oke.ugwu
 * Date: 26/11/13 12:03
 */

namespace Sidekick\Components\Notify;

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
   * @param string $name
   * @param array  $options
   */
  public function addFilter($name, $options)
  {
    $this->filters[$name] = $options;
  }

  /**
   * @param string $name
   *
   * @return array $options
   */
  public function getFilterOptions($name)
  {
    return $this->filters[$name];
  }

  /**
   * Accepts an associative array like this: $filters['name'] => 'value'
   *
   * @param array $filters
   */
  public function setFilters($filters)
  {
    $this->filters = $filters;
  }

  /**
   * Returns an associative array like this: $filters['name'] => 'value'
   *
   * @return array
   */
  public function getFilters()
  {
    return $this->filters;
  }
}
