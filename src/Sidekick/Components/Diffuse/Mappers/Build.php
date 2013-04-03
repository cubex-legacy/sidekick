<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Build extends RecordMapper
{
  public $name;
  public $description;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Enum\BuildLevel
   */
  public $buildLevel;
}
