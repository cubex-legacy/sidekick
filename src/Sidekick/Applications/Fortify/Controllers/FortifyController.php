<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Routing\StdRoute;
use Cubex\View\HtmlElement;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Fortify\Views\BuildRunDetails;
use Sidekick\Applications\Fortify\Views\BuildRunsList;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\BuildLog;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Fortify\Mappers\Command;
use Sidekick\Components\Projects\Mappers\Project;

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
    return new Sidebar($this->request()->path(2), $sidebarMenu);
  }

  public function renderIndex()
  {
    return new RenderGroup(
      '<h1>Thou shall fortify thy castle</h1>',
      '<p>Select a Project to fortify from the left</p>'
    );
  }

  public function renderFortify()
  {
    $projectId = $this->getInt('projectId');
    $buildType = $this->getInt('buildType', 1);
    $tabItems  = new Partial('<li class="%s"><a href="%s">%s</a></li>');
    $builds    = Build::collection()->loadAll();
    foreach($builds as $b)
    {
      $state = ($b->id() == $buildType) ? 'active' : '';
      $tabItems->addElement(
        $state,
        '/fortify/' . $projectId . '/' . $b->id(),
        $b->name
      );
    }

    $buttonGroup = $this->_buttonGroup(
      ['/builds' => 'Builds', '/commands' => 'Commands']
    );

    //list all build runs
    $allBuilds     = BuildRun::collection(
                       ['build_id' => $buildType, 'project_id' => $projectId]
                     )->setOrderBy('created_at', 'DESC');
    $allBuildsList = new BuildRunsList($allBuilds);

    return new RenderGroup(
      '<h1>Code Build and Testing</h1>',
      $buttonGroup,
      '<ul class="nav nav-tabs">',
      $tabItems,
      '</ul>',
      '<h1>Build History</h1>',
      $allBuildsList
    );
  }

  private function _buttonGroup($buttons = [])
  {
    $partial = new Partial(
      '<a class="btn" href="%s"><i class="icon-wrench"></i> %s</a>'
    );

    foreach($buttons as $href => $txt)
    {
      $partial->addElement($this->baseUri() . '/' . ltrim($href, '/'), $txt);
    }

    return new RenderGroup(
      new HtmlElement('div', ['class' => "pull-right btn-group"], $partial)
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
