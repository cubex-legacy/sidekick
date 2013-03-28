<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

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
   * @enumclass \Sidekick\Components\Diffuse\Mappers\VersionState
   */
  public $reviewStatus;
}
