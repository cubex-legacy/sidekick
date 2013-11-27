<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects;

use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use Sidekick\Applications\Diffuse\Views\Projects\OverviewView;
use Sidekick\Applications\Diffuse\Views\Projects\ProjectNav;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Projects\Mappers\Project;

class OverviewController extends DiffuseController
{
  protected $_projectId;

  public function preProcess()
  {
    $this->_projectId = $this->getProjectId();
  }

  public function renderIndex()
  {
    $project = new Project($this->_projectId);
    if($project->exists())
    {
      $versions = Version::collection(['project_id' => $this->_projectId])
      ->setOrderBy("id", "DESC")->setLimit(0, 50)->preFetch("platformStates");
      return new RenderGroup(
        $this->createView(new ProjectNav($this->appBaseUri(), $project)),
        $this->createView(
          new OverviewView($project, $versions, Platform::orderedCollection())
        )
      );
    }
    else
    {
      throw new \Exception("You seem to have stumbed upon.... nothing.");
    }
  }
}
