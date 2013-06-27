<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class ReleasePush extends RecordMapper
{
  public $releaseId;
  public $pushDate;
  public $completed = false;
  public $comment;
}
