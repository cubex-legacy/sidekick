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
  public $userId;
  public $contactMethod;
  /**
   * @datatype text
   */
  public $filters = [];

  protected $_autoTimestamp = false;

  protected function _configure()
  {
    $this->_setSerializer('filters');
  }
}
