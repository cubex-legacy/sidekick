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
use Sidekick\Applications\Fortify\Views\AddBuildCommandsForm;
use Sidekick\Applications\Fortify\Views\BuildCommands;
use Sidekick\Applications\Fortify\Views\FortifyForm;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\BuildsCommands;
use Sidekick\Components\Fortify\Mappers\Command;

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
