<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower\Mappers\Statistics;

use Cubex\Cassandra\CassandraMapper;

/**
 * Store state history at each point in time
 * $id = CHECKID-SERVERID-Y-m-d-h-i
 */
class StateHistory extends CassandraMapper
{
  public $checkId;
  public $serverId;
  public $stateTime;

  public $timeout;
  public $command;
  public $arguments;

  public $state;
  public $returnCode;
  public $rawResponse;

  public $startTime;
  public $endTime;

  public $executionTime;
  public $stateTimestamp;

  public $checkServerIp;
  public $checkServerHostname;

  public function id()
  {
    return sprintf(
      "%s:%s:%s",
      $this->checkId,
      $this->serverId,
      $this->stateTime
    );
  }

  protected function _configure()
  {
    $this->_addCompositeAttribute("id", ["checkId", "serverId", "stateTime"]);
  }
}
