<?php
/**
 * @author: oke.ugwu
 * Application:
 */
namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\TemplatedViewModel;

class ProjectList extends TemplatedViewModel
{
  protected $_projects;
  protected $_subProjects;
  protected $_configGroups;
  protected $_parentProject;

  public function setProjects($projects)
  {
    $this->_projects = $projects;
    return $this;
  }

  public function setSubProjects($subProjects)
  {
    $this->_subProjects = $subProjects;
    return $this;
  }

  public function setConfigGroups($configGroups)
  {
    $this->_configGroups = $configGroups;
    return $this;
  }

  public function setParentProject($project)
  {
    $this->_parentProject = $project;
    return $this;
  }

  public function getProjects()
  {
    return $this->_projects;
  }

  public function getParentProject()
  {
    return $this->_parentProject;
  }

  public function getSubProjectsCount($projectId)
  {
    if(isset($this->_subProjects[$projectId]))
    {
      return $this->_subProjects[$projectId];
    }
    return 0;
  }

  public function getConfigGroupsCount($projectId)
  {
    if(isset($this->_configGroups[$projectId]))
    {
      return $this->_configGroups[$projectId];
    }
    return 0;
  }
}