<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects;

use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\ProjectAwareBaseControl;
use Sidekick\Applications\Diffuse\Views\DeploymentHostsView;
use Sidekick\Components\Diffuse\Mappers\DeploymentConfig;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Servers\Mappers\Server;

class OverviewController extends ProjectAwareBaseControl
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
      $hosts     = Server::collection();
      $configs = DeploymentConfig::collection();

      return new RenderGroup(
        $this->createView(
          new DeploymentHostsView($project, $hosts, $configs)
        )
      );

      /*$versions = Version::collection(['project_id' => $this->_projectId])
      ->setOrderBy("id", "DESC")->setLimit(0, 50)->preFetch("platformStates");
      return new RenderGroup(
        $this->createView(new ProjectNav($this->appBaseUri(), $project)),
        $this->createView(
          new OverviewView($project, $versions, DeploymentConfig::collection())
        )
      );*/
    }
    else
    {
      throw new \Exception("You seem to have stumbed upon.... nothing.");
    }
  }

  public function postIndex()
  {
    die("process");
  }
}
