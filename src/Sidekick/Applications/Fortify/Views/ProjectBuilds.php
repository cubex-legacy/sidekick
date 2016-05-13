<?php
/**
 * Author: oke.ugwu
 * Date: 27/06/13 16:37
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Projects\Mappers\Project;

class ProjectBuilds extends TemplatedViewModel
{
  protected $_projects;
  protected $_builds;
  protected $_projectBuilds;

  public function __construct($projects, $builds, $projectBuilds)
  {
    $this->_projects   = $projects;
    $this->_builds = $builds;

    $this->_projectBuilds = [];
    foreach($projectBuilds as $p)
    {
      $this->_projectBuilds[] = $p->projectId . '-' . $p->buildId;
    }
  }

  /**
   * @return Project[]
   */
  public function getProjects()
  {
    return $this->_projects;
  }

  /**
   * @return Build[]
   */
  public function getBuilds()
  {
    return $this->_builds;
  }

  public function projectHasBuild($projectId, $buildId)
  {
    return in_array($projectId . '-' . $buildId, $this->_projectBuilds);
  }
}
