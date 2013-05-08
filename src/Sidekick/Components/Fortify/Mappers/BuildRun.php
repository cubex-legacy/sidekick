<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Data\Attribute;
use Cubex\Mapper\Database\RecordMapper;
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
}
