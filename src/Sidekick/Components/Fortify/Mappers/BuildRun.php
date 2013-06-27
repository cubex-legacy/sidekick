<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Data\Attribute\Attribute;
use Cubex\Mapper\Database\RecordMapper;
use Cubex\Sprintf\ParseQuery;
use Sidekick\Components\Fortify\Enums\BuildResult;
use Sidekick\Components\Fortify\Enums\BuildType;

class BuildRun extends RecordMapper
{
  public $projectId;
  public $buildId;
  /**
   * @enumclass \Sidekick\Components\Fortify\Enums\BuildResult
   */
  public $result;
  /**
   * @enumclass \Sidekick\Components\Fortify\Enums\BuildType
   */
  public $buildType = BuildType::REPOSITORY;
  /**
   * @var \DateTime
   */
  public $startTime;
  /**
   * @var \DateTime
   */
  public $endTime;
  public $commands;

  public $commitHash;

  protected function _configure()
  {
    $this->_attribute("commands")->setSerializer(
      Attribute::SERIALIZATION_JSON
    );
  }

  public function exited()
  {
    if($this->result === BuildResult::RUNNING)
    {
      $this->result  = BuildResult::ERROR;
      $this->endTime = new \DateTime();
      $this->saveChanges();
    }
  }

  public static function getLatestProjectBuilds()
  {
    $result = self::conn()->getRows(
      ParseQuery::parse(
        self::conn(),
        "SELECT * FROM %T WHERE %C IN
        (SELECT MAX(%C) FROM %T GROUP BY %C) ORDER BY %C DESC",
        self::tableName(),
        'id',
        'id',
        self::tableName(),
        'project_id',
        'start_time'
      )
    );

    return $result;
  }
}
