<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

/**
 * @unique branch_id,class,commit_hash
 */
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

  public $error;

  public $buildPath;
  public $scratchPath;

  protected function _configure()
  {
    $this->_setSerializer("configuration");
  }
}
