<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Projects\Mappers\Project;

class ProjectNav extends TemplatedViewModel
{
  protected $_project;
  protected $_baseUri;

  public function __construct($baseUri = null, Project $project = null)
  {
    $this->_baseUri = $baseUri;
    $this->_project = $project;
  }

  public function setBaseUri($uri)
  {
    $this->_baseUri = $uri;
    return $this;
  }

  public function getBaseUri()
  {
    return $this->_baseUri;
  }

  public function setProject(Project $project)
  {
    $this->_project = $project;
    return $this;
  }

  public function project()
  {
    return $this->_project;
  }
}
