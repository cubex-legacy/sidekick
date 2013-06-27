<?php
/**
 * Author: oke.ugwu
 * Date: 27/06/13 18:03
 */

namespace Sidekick\Applications\Fortify\Views;

use Sidekick\Components\Projects\Mappers\Project;

class FortifyHome extends BuildRunsList
{
  public function project($project_id)
  {
    return new Project($project_id);
  }
}
