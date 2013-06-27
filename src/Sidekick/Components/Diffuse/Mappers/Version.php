<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Diffuse\Enums\VersionState;

/**
 * Class Version
 */
class Version extends RecordMapper
{
  /**
   * @datatype tinyint
   */
  public $major;
  /**
   * @datatype smallint
   */
  public $minor;
  /**
   * @datatype smallint
   */
  public $build;

  /**
   * initiating build ID
   */
  public $buildId;
  /**
   * related project
   */
  public $projectId;
  /**
   * related repository
   */
  public $repoId;

  public $fromCommitHash;
  public $toCommitHash;

  /**
   * @datatype mediumtext
   */
  public $changeLog;

  /**
   * @enumclass \Sidekick\Components\Diffuse\Enums\VersionState
   */
  public $versionState = VersionState::UNKNOWN;

  public function actions()
  {
    return $this->hasMany(new Action());
  }
}
