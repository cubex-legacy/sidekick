<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Fortify\Enums\BuildStatus;

class CommitBuild extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;

  public $commit;
  public $branchId;

  /**
   * @enum
   * @enumclass \Sidekick\Components\Fortify\Enums\BuildStatus
   */
  public $status = BuildStatus::PENDING;
  public $startedAt;
  public $finishedAt;

  protected function _configure()
  {
    $this->_addCompositeAttribute("id", ["branchID", "commit"]);
  }
}
