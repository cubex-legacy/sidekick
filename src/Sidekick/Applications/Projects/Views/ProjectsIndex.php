<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 30/05/13
 * Time: 12:42
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Projects\Views;

use Cubex\View\TemplatedViewModel;

class ProjectsIndex extends TemplatedViewModel
{
  protected $_projects;

  public function __construct($projects)
  {
    $this->_projects = $projects;
  }

  /**
   * @return \Sidekick\Components\Projects\Mappers\Project[]
   */
  public function getProjects()
  {
    return $this->_projects;
  }
}
