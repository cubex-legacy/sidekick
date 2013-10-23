<?php
/**
 * Author: oke.ugwu
 * Date: 23/10/13 11:28
 */

namespace Sidekick\Applications\Evento\Views;

use Cubex\Data\Refine\Refinements\PropertyEqual;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Enums\Severity;

class EventoSubscribeIndex extends TemplatedViewModel
{
  protected $_eventTypes;
  /**
   * @var \Cubex\Mapper\Database\RecordCollection
   */
  protected $_eventSubscriptions;
  protected $_severityLookup;

  public function __construct($eventTypes, $eventSubcriptions)
  {
    $this->_eventTypes         = $eventTypes;
    $this->_eventSubscriptions = $eventSubcriptions;

    $this->requireJs('subscribe');
  }

  /**
   * @return \Sidekick\Components\Evento\Mappers\EventType[]
   */
  public function eventTypes()
  {
    return $this->_eventTypes;
  }

  /**
   * Returns an array of EventTypeIds current user is subscribed to
   *
   * @return array
   */
  public function eventSubscriptionIds()
  {
    return $this->_eventSubscriptions->getUniqueField('eventTypeId');
  }

  public function eventSubscriptions()
  {
    return $this->_eventSubscriptions;
  }

  public function getSeverityLookup()
  {
    if($this->_severityLookup == null)
    {
      $this->_severityLookup = $this->_eventSubscriptions->getKeyPair(
        "event_type_id",
        "severity"
      );
    }

    return $this->_severityLookup;
  }

  public function getSeverityList()
  {
    return (new Severity())->getConstList();
  }

  public function selected($eventTypeId, $selectValue)
  {
    $this->getSeverityLookup();
    return (isset($this->_severityLookup[$eventTypeId])
      && $this->_severityLookup[$eventTypeId] == $selectValue) ?
      'selected="selected"' : '';
  }
}
