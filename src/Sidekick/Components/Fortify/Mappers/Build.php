<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Build extends RecordMapper
{
  public $name;
  public $description;
  /**
   * @enumclass \Sidekick\Components\Fortify\Enum\BuildLevel
   */
  public $buildLevel;
  public $sourceDirectory = 'sourcecode/';
}
