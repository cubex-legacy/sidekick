<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

/**
 * Class Version
 */
class Version extends RecordMapper
{
  public $projectId;
  public $repoId;
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
  public $fromCommitHash;
  public $toCommitHash;
  public $releaseDate;
  public $stageReleaseDate;
  public $changeLog;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Mappers\VersionState
   */
  public $versionState;

}
