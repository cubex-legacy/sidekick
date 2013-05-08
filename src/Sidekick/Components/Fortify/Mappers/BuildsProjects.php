<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\PivotMapper;
use Sidekick\Components\Projects\Mappers\Project;

class BuildsProjects extends PivotMapper
{
  public $buildSourceId;

  protected function _configure()
  {
    $this->pivotOn(new Build(), new Project());
  }
}
