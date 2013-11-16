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
  public $checkId;
  public $serverId;

  public $firstOk; //First OK since !OK to track uptime
  public $lastTimeOk;
  public $lastTimeWarning;
  public $lastTimeCritical;
  public $lastTimeUnknown;
  public $output;
  public $performanceData;
  public $currentState;
  public $lastCheck;
  public $nextCheck;
  public $lastStateChange;
  public $checkExecutionTime;
}
