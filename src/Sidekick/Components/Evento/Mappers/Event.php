<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Evento\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Enums\Severity;

class Event extends RecordMapper
{
  public $name;
  /**
   * @datatype text
   */
  public $description;

  public $openedAt;
  public $closedAt;

  /**
   * @datatype int
   */
  public $eventTypeId;
  /**
   * @enumclass \Sidekick\Components\Evento\Enums\Severity
   */
  public $severity = Severity::LOW;
  /**
   * @datatype int
   */
  public $owner;

  public function eventType()
  {
    return new EventType($this->id());
  }

}
