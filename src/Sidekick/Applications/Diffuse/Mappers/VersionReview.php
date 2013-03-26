<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class VersionReview extends RecordMapper
{
  public $repositoryId;
  public $major;
  public $minor;
  public $build;
  public $approverId;
  public $message;
  /**
   * @enumclass \Sidekick\Applications\Diffuse\Mappers\VersionState
   */
  public $reviewStatus;
}
