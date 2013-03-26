<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Release extends RecordMapper
{
  public $repositoryId;
  public $liveVersion;
  public $stageVersion;
}
