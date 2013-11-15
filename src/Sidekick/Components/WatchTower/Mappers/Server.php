<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower\Mappers;

use Cubex\Cassandra\CassandraMapper;

class Server extends CassandraMapper
{
  public $hostname;
  public $ipv4;
  public $ipv6;
}
