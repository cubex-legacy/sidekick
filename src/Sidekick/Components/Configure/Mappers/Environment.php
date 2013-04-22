<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Configure\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Environment extends RecordMapper
{
  public $name;
  public $filename;
}
