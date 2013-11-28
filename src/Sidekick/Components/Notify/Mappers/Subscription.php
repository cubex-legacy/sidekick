<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Components\Notify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Subscription extends RecordMapper
{
  public $app;
  public $eventKey;
  public $eventType;
  public $userId;
  public $contactMethod;
  /**
   * @datatype text
   */
  public $filters = [];

  protected $_idType = self::ID_MANUAL;
  protected $_autoTimestamp = false;

  public function id()
  {
    return md5(
      sprintf(
        "%s-%s-%s-%s-%s",
        $this->app,
        $this->eventKey,
        $this->eventType,
        $this->userId,
        $this->contactMethod
      )
    );
  }

  protected function _configure()
  {
    $this->_setSerializer('filters');
  }
}
