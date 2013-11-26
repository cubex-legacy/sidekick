<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class EventTypes extends RecordMapper
{
  public $eventKey;
  public $eventDescription;
  public $eventApplications;
  /**
   * @enumclass \Sidekick\Components\Notify\Enums\EventType
   */
  public $eventType;
  public $eventParams;

  protected function _configure()
  {
    $this->_setSerializer("eventApplications");
    $this->_setSerializer("eventParams");
  }

  public function getTableName($plural = false)
  {
    return "notify_event_types";
  }
}
