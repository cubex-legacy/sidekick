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
use Sidekick\Components\Projects\Mappers\Project;

class ProjectsIndex extends TemplatedViewModel
{
  protected $_projects;

  public function __construct()
  {
    $this->_projects = Project::collection()->loadAll()
                       ->setOrderBy('name')->preFetch('parent');
  }

  public function getProjects()
  {
    return $this->_projects;
  }
}
