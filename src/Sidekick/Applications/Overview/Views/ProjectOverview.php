<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Overview\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Projects\Mappers\Project;

class ProjectOverview extends TemplatedViewModel
{
  protected $_project;

  public function __construct(Project $project)
  {
    $this->_project = $project;
  }

  public function getProject()
  {
    return $this->_project;
  }
}
