<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Enums\VersionType;

/**
 * Class Version
 * @link http://semver.org
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
   * e.g. 2 for RC2
   * @datatype smallint
   */
  public $revision;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Enums\VersionType
   */
  public $type = VersionType::STANDARD;

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

  public function pushes()
  {
    return $this->hasMany(new Push());
  }
}
