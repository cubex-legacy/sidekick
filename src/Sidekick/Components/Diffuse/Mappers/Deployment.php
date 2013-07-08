<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

/**
 * a deployment is when a version is sent out to a platform
 */
class Deployment extends RecordMapper
{
  public $versionId;
  public $platformId;
  public $projectId;
  public $deployedOn;
  public $completed = false;
  public $comment;

  public function platform()
  {
    return new Platform($this->platformId);
  }
}
