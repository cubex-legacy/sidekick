<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class BuildAnalysis extends RecordMapper
{
  public $branchId;
  public $commitHash;

  public $class;
  /**
   * @text
   */
  public $configuration;

  /**
   * @bool
   */
  public $running;
}
