<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower;

use Cubex\Components\IComponent;
use Sidekick\Components\WatchTower\Mappers\Server;
use Sidekick\Components\WatchTower\Models\LinuxServer;

class WatchTowerComponent implements IComponent
{
  public function name()
  {
    return "Watch Tower";
  }

  public function description()
  {
    return "Server monitoring and data collection";
  }

  /**
   * @param $hostname
   *
   * @return Interfaces\IServer
   */
  public static function getServerByHostname($hostname)
  {
    $hostname = strtolower($hostname);
    return (new LinuxServer())->loadWithMapper(
      Server::collection()->getByIndex("hostname", $hostname)->first()
    );
  }

  /**
   * @param $ip
   *
   * @return Interfaces\IServer
   */
  public static function getServerByIpv4($ip)
  {
    return (new LinuxServer())->loadWithMapper(
      Server::collection()->getByIndex("ipv4", $ip)->first()
    );
  }

  /**
   * @param $id
   *
   * @return Interfaces\IServer
   */
  public static function getServerById($id)
  {
    return (new LinuxServer())->loadWithMapper(new Server($id));
  }
}
