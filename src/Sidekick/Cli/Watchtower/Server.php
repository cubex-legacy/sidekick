<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Watchtower;

use Cubex\Cli\CliCommand;
use Cubex\Cubid\Cubid;
use Sidekick\Components\WatchTower\Interfaces\ILinuxServer;
use Sidekick\Components\WatchTower\WatchTowerComponent;
use Sidekick\Components\WatchTower\Mappers\Server as Srv;

class Server extends CliCommand
{
  /**
   * @valuerequired
   */
  public $ip;
  /**
   * @valuerequired
   */
  public $hostname;
  /**
   * @valuerequired
   */
  public $loadAverage;

  /**
   * @return int
   */
  public function execute()
  {
    $server = new Srv();
    $server->setId(Cubid::generateCubid($server));
    $server->hostname = "localhost";
    $server->ipv4     = "127.0.0.1";
    $server->saveChanges();
    echo "Created localhost server with id " . $server->id() . "\n";
  }

  public function createServer()
  {
    $this->hostname = strtolower($this->hostname);
    if(empty($this->hostname) || empty($this->ip))
    {
      throw new \Exception(
        "Hostname and IP must be provided '$this->ip,$this->hostname'"
      );
    }
    else
    {
      if(
      Srv::collection()->getByIndex("hostname", $this->hostname)->hasMappers()
      || Srv::collection()->getByIndex("ipv4", $this->ip)->hasMappers()
      )
      {
        throw new \Exception(
          "A server with the same hostname or IP already exists"
        );
      }
      else
      {
        $server = new Srv();
        $server->setId(Cubid::generateCubid($server));
        $server->hostname = $this->hostname;
        $server->ipv4     = $this->ip;
        $server->saveChanges();
        return "Server created with ID: " . $server->id() . "\n";
      }
    }
  }

  public function findByIp()
  {
    $server = WatchTowerComponent::getServerByIpv4($this->ip);
    var_dump_json($server);
  }

  public function storeLoadAverage()
  {
    if(!empty($this->ip))
    {
      $server = WatchTowerComponent::getServerByIpv4($this->ip);
    }
    else if(!empty($this->hostname))
    {
      $server = WatchTowerComponent::getServerByHostname($this->hostname);
    }
    else
    {
      throw new \Exception("Unable to locate server", 404);
    }

    if($server instanceof ILinuxServer)
    {
      $server->storeCurrentLoadAverage($this->loadAverage);
    }
  }
}
