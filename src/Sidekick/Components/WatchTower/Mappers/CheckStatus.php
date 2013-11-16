<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower\Mappers;

use Cubex\Mapper\Database\RecordMapper;

/**
 * @primary checkId,serverId
 */
class CheckStatus extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;

  public $checkId;
  public $serverId;

  public $lastTimeOk;
  public $lastTimeWarning;
  public $lastTimeCritical;
  public $lastTimeUnknown;
  public $lastStateChange;

  public $statusInformation;
  public $performanceData;

  public $currentState;
  public $checkExecutionTime;

  public $lastCheckTime;
  public $nextCheckTime;

  protected function _configure()
  {
    $this->_addCompositeAttribute("id", ["checkId", "serverId"]);
  }
}
