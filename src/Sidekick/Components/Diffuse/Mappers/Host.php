<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Host extends RecordMapper
{
  public $hostname;
  public $ipv4;
  public $ipv6;
  public $name;
}
