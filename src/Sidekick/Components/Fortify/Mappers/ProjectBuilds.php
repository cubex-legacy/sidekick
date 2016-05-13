<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\PivotMapper;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Source;

class ProjectBuilds extends PivotMapper
{
  protected function _configure()
  {
    $this->pivotOn(new Project(), new Build());
  }
}
