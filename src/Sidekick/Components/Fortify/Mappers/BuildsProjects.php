<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\PivotMapper;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Source;

class BuildsProjects extends PivotMapper
{
  public $buildSourceId;
  public $lastCommitHash;

  protected function _configure()
  {
    $this->pivotOn(new Build(), new Project());
  }

  public function buildSource()
  {
    return new Source($this->buildSourceId);
  }
}
