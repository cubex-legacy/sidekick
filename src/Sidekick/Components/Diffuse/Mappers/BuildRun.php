<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Diffuse\Enums\BuildResult;

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
