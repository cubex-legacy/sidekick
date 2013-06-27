<?php
/**
 * Author: oke.ugwu
 * Date: 27/06/13 18:03
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Projects\Mappers\Project;

class FortifyHome extends TemplatedViewModel
{
  public $builds;

  public function __construct($builds)
  {
    $this->builds = $builds;

  }

  public function project($project_id)
  {
    return new Project($project_id);
  }
}
