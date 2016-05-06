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
  public $buildId;
  public $platformId;
  public $userId;
  public $projectId;
  public $startedAt;
  public $deployedOn;
  public $completed = false;
  public $pending = false;
  public $passed = false;
  public $comment;
  public $hosts;
  public $deployBase;

  public function platform()
  {
    return new DeploymentConfig($this->platformId);
  }

  public function user()
  {
    return new User($this->userId);
  }
}
