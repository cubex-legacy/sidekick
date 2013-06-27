<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Sidekick\Enums\Consistency;

class ApprovalConfiguration extends RecordMapper
{
  public $projectId;

  public $developers = [];
  /**
   * @enumclass \Sidekick\Components\Sidekick\Enums\Consistency
   */
  public $developerSignoffs = Consistency::NONE;
  public $requireDeveloperSignoff = false;

  public $managers = [];
  /**
   * @enumclass \Sidekick\Components\Sidekick\Enums\Consistency
   */
  public $managerSignoffs = Consistency::NONE;
  public $requireManagerSignoff = false;

  protected function _configure()
  {
    $this->_setSerializer("developers");
    $this->_setSerializer("managers");
  }
}
