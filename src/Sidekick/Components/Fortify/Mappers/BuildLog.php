<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Cassandra\CassandraMapper;
use Cubex\Log\Log;

class BuildLog extends CassandraMapper
{
  protected $_outputBuffers = false;
  public $buildRunId;
  /**
   * @datatype smallint
   */
  public $exitCode;
  public $startTime;
  public $endTime;

  public function enableOutput()
  {
    $this->_outputBuffers = true;
    return $this;
  }

  public function disableOutput()
  {
    $this->_outputBuffers = false;
    return $this;
  }

  public function isOutputEnabled()
  {
    return $this->_outputBuffers;
  }

  public function writeBuffer($type, $buffer)
  {
    $this->setData("output:" . (string)microtime(true) . ':' . $type, $buffer);
    $this->saveChanges();

    if($this->isOutputEnabled())
    {
      Log::debug($type . " - " . $buffer);
    }

    return $this;
  }
}
