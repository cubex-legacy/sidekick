<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Mappers;


use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Notify\Enums\NotifyType;

class EventHooks extends RecordMapper
{
  public $eventKey;
  /**
   * @enumclass \Sidekick\Components\Notify\Enums\NotifyType
   */
  public $notifyType;
  public $notifyUsers;
  public $notifyGroups;

  protected function _configure()
  {
    $this->_setSerializer("notifyType");
    $this->_setSerializer("notifyUsers");
    $this->_setSerializer("notifyGroups");
  }

  public function notifyTypes()
  {
    return new NotifyType();
  }

  public function getTableName($plural = false)
  {
    return "notify_event_hooks";
  }
}
