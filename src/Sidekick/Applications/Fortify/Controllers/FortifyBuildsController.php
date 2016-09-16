<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 07/06/13
 * Time: 11:25
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Views\MappersTable;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Fortify\Views\AddBuildCommandsForm;
use Sidekick\Applications\Fortify\Views\BuildCommands;
use Sidekick\Applications\Fortify\Views\FortifyForm;
use Sidekick\Applications\Fortify\Views\FortifyMapperList;
use Sidekick\Applications\Fortify\Views\ProjectBuildsView;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\BuildsCommands;
use Sidekick\Components\Fortify\Mappers\ProjectBuilds;
use Sidekick\Components\Fortify\Mappers\Command;
use Sidekick\Components\Projects\Mappers\Project;

class FortifyBuildsController extends FortifyCrudController
{
  protected $_title = 'Build';

  public function __construct()
  {
    parent::__construct(
      new Build(),
      ['name', 'description', 'build_level', 'source_directory']
    );
  }

  public function getSidebar()
  {
    return new Sidebar(
      $this->request()->path(3),
      [
        $this->appBaseUri() . '/build-configs/builds'   => 'Builds',
        $this->appBaseUri() . '/build-configs/commands' => 'Commands',
        $this->appBaseUri() . '/build-configs/projects' => 'Project Builds',
      ]
    );
  }

  public function renderIndex($page = 1)
  {
    $collection = new RecordCollection($this->_mapper);
    $collection->loadAll();

    //cloning because if i count it loads the collection then i can't limit
    $cloneToCount = clone $collection;
    $count        = $cloneToCount->count();
    $paginator    = $this->_getPaginator($page, $count, $this->_perPage);
    $offset       = $paginator->getOffset();
    $collection->setLimit($offset, $this->_perPage);

    $mapperTable = new MappersTable(
      $this->baseUri(), $collection, $this->_listColumns
    );

    return $this->createView(
      new FortifyMapperList(
        $this->_title,
        $mapperTable,
        $paginator,
        $this->baseUri(),
        $this->getAlert()
      )
    );
  }

  public function renderCommandsIndex($page = 1)
  {
    $commands = Command::collection()->loadAll();

    //cloning because if i count it loads the collection then i can't limit
    $cloneToCount = clone $commands;
    $count        = $cloneToCount->count();
    $paginator    = $this->_getPaginator($page, $count, $this->_perPage);
    $offset       = $paginator->getOffset();
    $commands->setLimit($offset, $this->_perPage);

    $mapperTable = new MappersTable(
      $this->baseUri(), $commands, ['name', 'command']
    );

    return $this->createView(
      new FortifyMapperList(
        'Command',
        $mapperTable,
        $paginator,
        $this->baseUri(),
        $this->getAlert()
      )
    );
  }

  public function renderProjects()
  {
    $this->requireJs('projectBuilds');
    $builds        = Build::collection();
    $projects      = Project::collection();
    $projectBuilds = ProjectBuilds::collection();

    return new ProjectBuildsView($projects, $builds, $projectBuilds);
  }

  public function postProjects()
  {
    $postData = $this->request()->postVariables();
    if(isset($postData['value']))
    {
      list($projectId, $buildId) = explode('-', $postData['value']);
      if((int)$projectId > 0 && (int)$buildId > 0)
      {
        $projectBuild = new ProjectBuilds([$projectId, $buildId]);
        if($postData['add'] == 'true')
        {
          $projectBuild->buildId   = $buildId;
          $projectBuild->projectId = $projectId;
          $projectBuild->saveChanges();
        }
        else
        {
          $projectBuild->delete();
        }

        return ["done"];
      }
    }
    return ['failed'];
  }

  public function renderShow($id = 0)
  {
    $this->_mapper->load($id);
    $tbl = $this->createView(
      new MappersTable(
        $this->baseUri(),
        (new RecordCollection($this->_mapper, [$this->_mapper])),
        $this->_listColumns
      )
    );
    return new RenderGroup($this->mapperNav(), $tbl);
  }

  public function renderNew()
  {
    return new RenderGroup(
      '<h1>New ' . $this->_title . '</h1>',
      $this->createView(new FortifyForm($this->_mapper, $this->baseUri()))
    );
  }

  public function renderEdit($id = 0)
  {
    $this->requireCss('buildCommandModal');

    $this->_mapper->load($id);

    $allCommands = Command::collection()->loadAll()->getKeyPair(
      'id',
      'name'
    );

    $buildCommands = BuildsCommands::collection(['build_id' => $id]);
    if($buildCommands->count())
    {
      $buildCommandsIds   = $buildCommands->getUniqueField("command_id");
      $unAssignedCommands = Command::collection()->loadWhere(
        "%C NOT IN (%Ld)",
        "id",
        $buildCommandsIds
      )->getKeyPair('id', 'name');
    }
    else
    {
      $unAssignedCommands = $allCommands;
    }

    $buildCommandsView   = $this->createView(
      new BuildCommands($buildCommands)
    );
    $addCommandModalForm = $this->createView(
      new AddBuildCommandsForm(
        $id,
        $unAssignedCommands,
        $allCommands
      )
    );

    return new RenderGroup(
      $this->mapperNav(),
      $this->createView(new FortifyForm($this->_mapper, $this->baseUri())),
      $addCommandModalForm,
      $buildCommandsView
    );
  }
}
