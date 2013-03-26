<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Repository extends RecordMapper
{
  public $projectId;
  public $versionControlSystem;
  public $path;
  /**
   * @enumclass \Sidekick\Applications\Diffuse\Mappers\PushType
   */
  public $pushType;
}
