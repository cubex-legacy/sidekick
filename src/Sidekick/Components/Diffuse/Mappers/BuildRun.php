<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class BuildRun extends RecordMapper
{
  public $projectId;
  public $buildId;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Enums\BuildResult
   */
  public $result;
  public $startTime;
  public $endTime;

  /**
   * @datatype tinyint
   */
  public $versionMinor;
  /**
   * @datatype smallint
   */
  public $versionMajor;
  /**
   * @datatype smallint
   */
  public $versionBuild;
}
