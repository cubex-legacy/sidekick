<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Facade\Queue;
use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\Queue\StdQueue;
use Cubex\Routing\StdRoute;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedView;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Fortify\Views\BuildRunDetails;
use Sidekick\Applications\Fortify\Views\BuildsPage;
use Sidekick\Applications\Fortify\Views\FortifyRepositoryLink;
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
    $projectId    = $this->getInt('projectId');
    $buildType    = $this->getInt('buildType', 1);
    $resultFilter = $this->getStr('result');

    $collectionFilter = ['build_id' => $buildType, 'project_id' => $projectId];
    if($resultFilter !== null)
    {
      $collectionFilter['result'] = $resultFilter;
    }

    $builds = Build::collection()->loadAll();
    //list all build runs
    $allBuilds = BuildRun::collection($collectionFilter)->setOrderBy(
      'created_at',
      'DESC'
    );

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
      $commandRun    = BuildLog::cf()->get("$runId-$c", ['exit_code','start_time']);
      $commandOutput = BuildLog::cf()->getSlice("$runId-$c", 'output:0', '', false, 1000);

      $view->addCommand(
        new Command($c),
        $commandRun,
        in_array($commandRun['exit_code'], $command->successExitCodes),
        $commandOutput
      );
    }

    $this->requireJs('buildLog');
    return $view;
  }

  public function renderRepo()
  {
    $projectId = $this->getInt('projectId');
    $buildId   = $this->getInt('buildType');

    $buildRepo = BuildsProjects::collection()->loadOneWhere(
      ['project_id' => $projectId, 'build_id' => $buildId]
    );

    $repos       = Source::collection()->loadAll()->getKeyedArray(
      'id',
      ['name', 'branch']
    );
    $repoOptions = [];
    foreach($repos as $id => $info)
    {
      $repoOptions[$id] = $info['name'] . ' - ' . $info['branch'];
    }

    $project = new Project($projectId);
    $repo    = new Source($buildRepo->buildSourceId);
    $build   = new Build($buildId);

    return new FortifyRepositoryLink($project, $repo, $build, $repoOptions);
  }

  /*
   * Run build process. Does not actually run the build, it only puts
   * the request into a queue, which gets processed by cron script
   */
  public function renderBuild()
  {
    $projectId = $this->getInt('projectId');
    $buildId   = $this->getInt('buildType');

    $buildRepo = BuildsProjects::collection()->loadOneWhere(
      ['project_id' => $projectId, 'build_id' => $buildId]
    );

    $queue = new StdQueue('buildRequest');
    Queue::push(
      $queue,
      ['respositoryId' => $buildRepo->buildSourceId, 'buildId' => $buildId]
    );

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Your Build Request has been queued up!';

    Redirect::to($this->baseUri() . '/' . $projectId . '/' . $buildId)->with(
      'msg',
      $msg
    )->now();
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
      new StdRoute('/:projectId/:buildType/repository', 'renderRepo'),
      new StdRoute('/:projectId/:buildType/build', 'renderBuild'),
      new StdRoute('/:projectId/:buildType/:runId@num', 'runDetails'),
      new StdRoute('/:projectId/:buildType/(?<result>(pass|fail|running))/', 'fortify')
    );

    return $routes;
  }
}
