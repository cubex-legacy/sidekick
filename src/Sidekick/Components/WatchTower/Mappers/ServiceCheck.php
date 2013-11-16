<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class ServiceCheck extends RecordMapper
{
  public $name;
  public $description;
  public $groupId;
  public $checkInterval = 1;
  public $command;
  public $arguments;
}
