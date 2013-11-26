<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Facade\Queue;
use Cubex\Facade\Redirect;
use Cubex\Facade\Session;
use Cubex\Form\Form;
use Cubex\Mapper\Collection;
use Cubex\Queue\StdQueue;
use Cubex\Routing\StdRoute;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Fortify\Reports\FortifyReport;
use Sidekick\Applications\Fortify\Views\BuildChanges;
use Sidekick\Applications\Fortify\Views\BuildDetailsView;
use Sidekick\Applications\Fortify\Views\BuildLogView;
use Sidekick\Applications\Fortify\Views\BuildRunPage;
use Sidekick\Applications\Fortify\Views\BuildsPage;
use Sidekick\Applications\Fortify\Views\FortifyHome;
use Sidekick\Applications\Fortify\Views\FortifyRepositoryLink;
use Sidekick\Applications\Fortify\Views\ReportErrorPage;
use Sidekick\Components\Fortify\FortifyBuildChanges;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\BuildLog;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Fortify\Mappers\BuildsCommands;
use Sidekick\Components\Fortify\Mappers\BuildsProjects;
use Sidekick\Components\Fortify\Mappers\Command;
use Sidekick\Components\Helpers\BuildCommandsHelper;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Source;
use Thrift\Exception\TTransportException;

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
    $builds = BuildRun::getLatestProjectBuilds();
    return new FortifyHome($builds);
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

  public function buildDetails()
  {
    $runId    = $this->getInt('runId');
    $buildId  = $this->getInt('buildType');
    $buildRun = new BuildRun($runId);
    $build    = new Build($buildId);

    /**
     * Get builds commands and order them by dependecies before passing to view
     * so commands get displayed in the right order
     */
    $buildCommands = BuildsCommands::collection(['build_id' => $buildId]);
    $buildCommands = BuildCommandsHelper::orderByDependencies($buildCommands);

    /**
     * Get just the command id. That's all the view needs
     */
    $collection    = new Collection(new BuildsCommands(), $buildCommands);
    $buildCommands = $collection->getFieldValues('command_id');

    $basePath   = $this->request()->path(4);
    $currentTab = $this->request()->offsetPath(4, 1);
    $view       = new BuildDetailsView($buildRun, $basePath);
    $view       = $this->_addCommandToView(
      $buildCommands,
      $runId,
      $view
    );

    return new BuildRunPage($view, $buildRun, $build, $basePath, $currentTab);
  }

  public function renderBuildLog()
  {
    $runId      = $this->getInt('runId');
    $buildId    = $this->getInt('buildId');
    $buildRun   = new BuildRun($runId);
    $build      = new Build($buildId);
    $basePath   = $this->request()->path(4);
    $currentTab = $this->request()->offsetPath(4, 1);
    $view       = new BuildLogView($runId);
    $view       = $this->_addCommandToView($buildRun->commands, $runId, $view);

    $this->requireJs('buildLog');
    $commandId = $this->getInt('commandId');

    /**
     * If we are viewing build log for a single command, keep the log output
     * expanded by default
     */
    if($commandId !== null)
    {
      $command = new Command($commandId);
      $this->addJsBlock(
        "$('#" . md5($command->command) . "').show();
        $('#" . md5($command->command) . "-trigger').text('-');"
      );
    }
    return new BuildRunPage($view, $buildRun, $build, $basePath, $currentTab);
  }

  public function renderChanges()
  {
    $runId     = $this->getInt('runId');
    $buildId   = $this->getInt('buildType');
    $projectId = $this->getInt('projectId');

    $buildRun   = new BuildRun($runId);
    $build      = new Build($buildId);
    $basePath   = $this->request()->path(4);
    $currentTab = $this->request()->offsetPath(4);

    $changes = new FortifyBuildChanges(
      $projectId, $buildId, $buildRun->commitHash, $runId
    );
    $commits = $changes->buildCommitRange();

    $repo = (new Project($projectId))->repository();
    $view = $this->createView(new BuildChanges($repo, $runId, $commits));

    return new BuildRunPage($view, $buildRun, $build, $basePath, $currentTab);
  }

  public function renderReport()
  {
    $commandId = $this->getStr('commandId');

    $filter   = $this->getStr('filter');
    $runId    = $this->getInt('runId');
    $buildId  = $this->getInt('buildType');
    $basePath = $this->request()->path(5);

    $build = new Build($buildId);

    $command = new Command($commandId);
    if($command->reportNamespace !== null)
    {
      try
      {
        $className = FortifyReport::getReportProviderClass(
          $command->reportNamespace
        );

        $reportProvider = new $className($buildId, $runId, $filter, $basePath);

        if($reportProvider instanceof FortifyReport)
        {
          if($reportProvider->reportFileExists())
          {
            $report   = $reportProvider->getView();
            $basePath = $this->request()->path(4);
            return new BuildRunPage(
              $report, new BuildRun($runId), $build, $basePath
            );
          }
          else
          {
            return new ReportErrorPage(
              $command->name,
              ($reportProvider->getReportFile() . ' does not exist'),
              $this->request()->path(4)
            );
          }
        }
      }
      catch(\Exception $e)
      {
        return new ReportErrorPage(
          $command->name,
          ($command->reportNamespace . $e->getMessage()),
          $this->request()->path(4)
        );
      }
    }
    else
    {
      return new ReportErrorPage(
        $command->name,
        'No report namespace is configured',
        $this->request()->path(4)
      );
    }
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
  public function build()
  {
    $projectId = $this->getInt('projectId');
    $buildId   = $this->getInt('buildType');

    try
    {
      $repoId = 0;

      $buildRepo = BuildsProjects::collection()->loadOneWhere(
        ['project_id' => $projectId, 'build_id' => $buildId]
      );

      if($buildRepo === null)
      {
        //try to get the master repo for project
        $project   = new Project($projectId);
        $buildRepo = $project->repository();
        if($buildRepo->exists())
        {
          $repoId = $buildRepo->id();
        }
      }
      else if($buildRepo->exists())
      {
        $repoId = $buildRepo->buildSourceId;
      }

      if($repoId > 0)
      {
        $queue = new StdQueue('buildRequest');
        Queue::push(
          $queue,
          ['respositoryId' => $repoId, 'buildId' => $buildId]
        );

        $msg       = new \stdClass();
        $msg->type = 'success';
        $msg->text = 'Your Build Request has been queued up!';
      }
      else
      {
        $msg       = new \stdClass();
        $msg->type = 'error';
        $msg->text = 'Your Build Request could not be processed.' .
        ' No Repository is linked to this build type';
      }
    }
    catch(\Exception $e)
    {
      /*
       * By the way, I think getting to this point is impossible, because
       * BuildsProject Mapper has projectId and buildId as primary key, so
       * any combination of these two keys should always return one result.
       * The only case this will happen is if the primary keys got changed
       * and this is very unlikely.
       */
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = 'Your Build Request could not be processed.' .
      'More than one Repository is linked to this build type';
    }

    Redirect::to($this->baseUri() . '/' . $projectId . '/' . $buildId)
    ->with('msg', $msg)->now();
  }

  private function _addCommandToView(
    $commands, $runId, TemplatedViewModel $view
  )
  {
    try
    {
      //determine if user has requested for a filtered command list
      $filter = $this->getStr('commandId');
      foreach($commands as $c)
      {
        if($filter !== null && $filter != $c)
        {
          continue;
        }
        $command       = new Command($c);
        $commandRun    = BuildLog::cf()->get(
          "$runId-$c",
          ['exit_code', 'start_time', 'end_time']
        );
        $commandOutput = BuildLog::cf()->getSlice(
          "$runId-$c",
          'output:0',
          'output:z',
          false,
          1000
        );

        $view->addCommand(
          new Command($c),
          $commandRun,
          in_array(idx($commandRun, 'exit_code'), $command->successExitCodes),
          $commandOutput
        );
      }

      return $view;
    }
    catch(TTransportException $e)
    {
      return "Sidekick could not connect to cassandra to read " .
      "command output. Please make sure cassandra service is running";
    }
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
      new StdRoute('/:projectId/:buildType/repository', 'repo'),
      new StdRoute('/:projectId/:buildType/build', 'build'),
      new StdRoute('/:projectId/:buildType/:runId@num/', 'buildDetails'),
      new StdRoute('/:projectId/:buildType/:runId@num/buildlog', 'buildLog'),
      new StdRoute(
        '/:projectId/:buildType/:runId@num/buildlog/:commandId',
        'buildLog'
      ),
      new StdRoute(
        '/:projectId/:buildType/:runId@num/changes',
        'changes'
      ),
      new StdRoute(
        '/:projectId/:buildType/:runId@num/:commandId',
        'report'
      ),
      new StdRoute(
        '/:projectId/:buildType/:runId@num/:commandId/:filter',
        'report'
      ),
      new StdRoute(
        '/:projectId/:buildType/(?<result>(pass|fail|running))/',
        'fortify'
      )
    );

    return $routes;
  }
}
