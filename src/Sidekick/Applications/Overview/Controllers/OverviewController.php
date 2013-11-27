<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Overview\Controllers;

use Cubex\Facade\Redirect;
use Cubex\View\TemplatedView;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Overview\Views\ProjectOverview;
use Sidekick\Applications\Overview\Views\ProjectSelector;
use Sidekick\Applications\Overview\Views\Releases;
use Sidekick\Components\Projects\Mappers\Project;

class OverviewController extends BaseControl
{
  public function renderIndex()
  {
    $projectId = $this->application()->project()->getProjectId();
    if($projectId > 0)
    {
      return new ProjectOverview(new Project($projectId));
    }

    $projects = Project::collection();
    if($projects->hasMappers())
    {
      return new ProjectSelector($projects);
    }
    else
    {
      return new TemplatedView('Homepage', $this);
    }
  }

  public function getSidebar()
  {
    return null;
  }

  public function renderReleases()
  {
    return new Releases();
  }

  public function logout()
  {
    \Auth::logout();
    Redirect::to('/')->now();
  }

  public function getRoutes()
  {
    return [
      '/releases' => 'releases',
      'logout'    => 'logout'
    ];
  }
}
