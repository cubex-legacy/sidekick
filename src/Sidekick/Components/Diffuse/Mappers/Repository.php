<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Repository extends RecordMapper
{
  public $projectId;
  public $versionControlSystem;
  public $path;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Mappers\PushType
   */
  public $pushType;
}
