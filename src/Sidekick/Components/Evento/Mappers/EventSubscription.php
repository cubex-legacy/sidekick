<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Evento\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class EventSubscription extends RecordMapper
{
  public $eventTypeId;
  public $userId;
  /**
   * @enumclass \Sidekick\Components\Evento\Enums\Severity
   */
  public $severity;

  protected $_idType = self::ID_COMPOSITE;
  protected $_autoTimestamp = false;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      array(
           "eventTypeId",
           "userId"
      )
    );
  }
}
