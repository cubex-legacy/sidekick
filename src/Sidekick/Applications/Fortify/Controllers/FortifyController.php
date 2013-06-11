<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Form\Form;
use Cubex\Routing\StdRoute;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedView;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Fortify\Views\BuildRunDetails;
use Sidekick\Applications\Fortify\Views\BuildsPage;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\BuildLog;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Fortify\Mappers\BuildsProjects;
use Sidekick\Components\Fortify\Mappers\Command;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Source;

class FortifyController extends BaseControl
{

  public function getSidebar()
  {
    $projects    = Project::collection()->loadAll()->setOrderBy('name');
    $sidebarMenu = [];
    foreach($projects as $project)
    {
      $sidebarMenu['/fortify/' . $project->id] = $project->name;
    }

    $main = new Sidebar(
      $this->request()->path(2),
      [
      '/fortify/builds'   => 'Manage Builds',
      '/fortify/commands' => 'Manage Commands'
      ]
    );

    return new RenderGroup(
      $main,
      '<hr>',
      new Sidebar($this->request()->path(2), $sidebarMenu)
    );
  }

  public function renderIndex()
  {
    return new TemplatedView("Index", $this);
  }

  public function renderFortify()
  {
    $projectId = $this->getInt('projectId');
    $buildType = $this->getInt('buildType', 1);

    $builds = Build::collection()->loadAll();
    //list all build runs
    $allBuilds = BuildRun::collection(
                   ['build_id' => $buildType, 'project_id' => $projectId]
                 )->setOrderBy('created_at', 'DESC');

    return $this->createView(
      new BuildsPage(
        $projectId, $buildType, $builds, $allBuilds
      )
    );
  }

  public function renderRunDetails()
  {
    $runId    = $this->getInt('runId');
    $buildRun = new BuildRun($runId);

    $view = new BuildRunDetails();

    foreach($buildRun->commands as $c)
    {
      $command       = new Command($c);
      $commandRun    = BuildLog::cf()->get("$runId-$c", ['exit_code']);
      $commandOutput = BuildLog::cf()->getSlice("$runId-$c", 'output:0');

      $view->addCommand(
        new Command($c),
        in_array($commandRun['exit_code'], $command->successExitCodes),
        $commandRun['exit_code'],
        $commandOutput
      );
    }

    return $view;
  }

  public function getRoutes()
  {
    //extending ResourceTemplate routes
    $routes = parent::getRoutes();

    //put overrides on top of routes so they take priority
    array_unshift(
      $routes,
      new StdRoute('/:projectId', 'fortify'),
      new StdRoute('/:projectId/:buildType', 'fortify'),
      new StdRoute('/:projectId/:buildType/:runId', 'runDetails')
    );

    return $routes;
  }
}
