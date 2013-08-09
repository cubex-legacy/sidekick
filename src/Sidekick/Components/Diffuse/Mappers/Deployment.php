<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Users\Mappers\User;

/**
 * a deployment is when a version is sent out to a platform
 */
class Deployment extends RecordMapper
{
  public $versionId;
  public $platformId;
  public $userId;
  public $projectId;
  public $deployedOn;
  public $completed = false;
  public $comment;

  public function platform()
  {
    return new Platform($this->platformId);
  }

  public function user()
  {
    return new User($this->userId);
  }
}
