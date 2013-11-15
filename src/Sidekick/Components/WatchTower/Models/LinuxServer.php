<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower\Models;

use Sidekick\Components\WatchTower\Interfaces\ILinuxServer;
use Sidekick\Components\WatchTower\Mappers\Server;
use Sidekick\Components\WatchTower\Mappers\ServerStatistic;

class LinuxServer implements ILinuxServer
{
  /**
   * @var Server
   */
  protected $_serverMapper;
  protected $_serverId;

  public function __construct()
  {
  }

  public function loadById($serverId)
  {
    $this->_serverId     = $serverId;
    $this->_serverMapper = new Server($serverId);
    return $this;
  }

  public function loadWithMapper($server)
  {
    if($server instanceof Server)
    {
      $this->_serverMapper = $server;
      $this->_serverId     = $server->id();
    }
    else
    {
      throw new \Exception("Invalid mapper sent to load with mapper", 500);
    }
    return $this;
  }

  protected function _checkMapper()
  {
    if($this->_serverMapper === null)
    {
      throw new \Exception("Server Mapper is unavailable");
    }
  }

  /**
   * @param string $loadAverage e.g. "1.0 0.0 0.0"
   *
   * @return mixed
   */
  public function storeCurrentLoadAverage($loadAverage)
  {
    $this->_checkMapper();
    //Store historical data
    ServerStatistic::cf()->insert(
      "load-{$this->_serverId}-" . date("Y-m-d"),
      [strtotime(date("Y-m-d H:i:00")) => $loadAverage]
    );

    //Store current version for reading
    $this->_serverMapper->setData("loadAverage", $loadAverage);
    $this->_serverMapper->saveChanges();
  }

  /**
   * Store running processes for a specific process name
   *
   * @param $process
   * @param $count
   *
   * @return mixed
   */
  public function storeProcessCount($process, $count)
  {
    $this->_checkMapper();
    //Store historical data
    ServerStatistic::cf()->insert(
      "process-{$process}-{$this->_serverId}-" . date("Y-m-d"),
      [strtotime(date("Y-m-d H:i:00")) => $count]
    );

    //Store current version for reading
    $this->_serverMapper->setData("process:$process:count", $count);
    $this->_serverMapper->saveChanges();
  }


  public function getHostname()
  {
    $this->_checkMapper();
    return $this->_serverMapper->hostname;
  }

  public function getIpv4()
  {
    $this->_checkMapper();
    return $this->_serverMapper->ipv4;
  }

  public function getIpv6()
  {
    $this->_checkMapper();
    return $this->_serverMapper->ipv6;
  }
}
