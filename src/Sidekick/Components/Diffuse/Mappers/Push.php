<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

/**
 * a push is when a version is sent out to a platform
 */
class Push extends RecordMapper
{
  public $versionId;
  public $platformId;
  public $pushDate;
  public $completed = false;
  public $comment;
}
