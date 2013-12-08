<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class CommitBuild extends RecordMapper
{
  public $commit;
  public $branchId;

  public $state;
  public $startedAt;
  public $finishedAt;

  protected function _configure()
  {
    $this->_addCompositeAttribute("id", ["branchID", "commit"]);
  }
} 