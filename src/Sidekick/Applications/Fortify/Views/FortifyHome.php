<?php
/**
 * Author: oke.ugwu
 * Date: 27/06/13 18:03
 */

namespace Sidekick\Applications\Fortify\Views;

use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Projects\Mappers\Project;

class FortifyHome extends BuildRunsList
{
  public function project($projectId)
  {
    return new Project($projectId);
  }

  public function build($buildId)
  {
    return new Build($buildId);
  }
}
