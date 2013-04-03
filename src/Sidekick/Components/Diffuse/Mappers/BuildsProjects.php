<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\PivotMapper;
use Sidekick\Components\Projects\Mappers\Project;

class BuildsProjects extends PivotMapper
{
  protected function _configure()
  {
    $this->pivotOn(new Build(), new Project());
  }
}
