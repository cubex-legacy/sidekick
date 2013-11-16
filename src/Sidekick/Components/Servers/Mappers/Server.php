<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Servers\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Servers\Enums\ConnectType;

class Server extends RecordMapper
{
  public $name;
  /**
   * @unique
   */
  public $hostname;
  public $ipv4;
  public $ipv6;
  public $sshUser = 'root';
  public $sshPort = 22;

  /**
   * @enumclass ConnectType
   */
  public $preferConnection = ConnectType::IPV4;

  public function getConnPreference()
  {
    switch($this->preferConnection)
    {
      case ConnectType::HOSTNAME:
        if($this->hostname !== null)
        {
          return $this->hostname;
        }
        break;
      case ConnectType::IPV4:
        if($this->ipv4 !== null)
        {
          return $this->ipv4;
        }
        break;
      case ConnectType::IPV6:
        if($this->ipv6 !== null)
        {
          return $this->ipv6;
        }
        break;
      case ConnectType::IP:
        if($this->ipv4 !== null)
        {
          return $this->ipv4;
        }
        else if($this->ipv6 !== null)
        {
          return $this->ipv6;
        }
        break;
      default:
        return $this->ipv4;
    }
    return null;
  }
}
