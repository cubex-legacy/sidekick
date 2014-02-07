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
use Sidekick\Components\Fortify\Mappers\CommitBuild;
use Sidekick\Components\Fortify\Mappers\CommitBuildInsight;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Repository;

class OverviewController extends BaseControl
{
  public function renderIndex()
  {
    $projectId = $this->application()->project()->getProjectId();
    if($projectId > 0)
    {
      return $this->renderProject($projectId);
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

  public function renderProject($projectId)
  {
    $project = new Project($projectId);

    $repository = Repository::loadWhere(["projectId" => $projectId]);

    $branch = Branch::loadWhere(
      [
        "branch"       => "master",
        "repositoryId" => $repository->id
      ]
    );

    $commitBuilds = CommitBuild::collection(
      [
        "branchId" => $branch->id
      ]
    )
      ->setOrderBy('startedAt', "DESC")
      ->setLimit(0, 5);

    $insight           = new CommitBuildInsight();
    $insight->branchId = $branch->id;
    $insight->commit   = $commitBuilds->first()->commit;
    $insight->load($insight->id());

    $view = new ProjectOverview($project);
    if($repository)
    {
      $view->setRepository($repository);
    }
    if($branch)
    {
      $view->setBranch($branch);
    }
    if($commitBuilds)
    {
      $view->setCommitBuilds($commitBuilds);
    }
    if($insight)
    {
      $view->setInsight($insight);
    }

    echo $this->createView($view);
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
