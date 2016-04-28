<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Overview\Views;

use Cubex\Mapper\Collection;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Projects\Mappers\Project;

class ProjectSelector extends TemplatedViewModel
{
  protected $_projects = [];

  public function __construct(Collection $projects)
  {
    if($projects)
    {
      foreach($projects as $project)
      {
        /**
         * @var $project Project
         */
        if(strstr($project->name, ':'))
        {
          list($section, $name) = explode(":", $project->name);
          $project->name = $name;
        }
        else
        {
          $section = null;
        }

        if(!isset($this->_projects[$section]))
        {
          $this->_projects[$section] = [];
        }

        $this->_projects[$section][] = $project;
      }
      ksort($this->_projects);
    }
  }

  public function sections()
  {
    return array_keys($this->_projects);
  }

  /**
   * @param null $section
   *
   * @return Project[]|Collection
   */
  public function projects($section = null)
  {
    return $this->_projects[$section];
  }
}
