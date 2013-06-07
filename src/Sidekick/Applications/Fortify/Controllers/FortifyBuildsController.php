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
    $tbl = new MappersTable(
      $this->baseUri(),
      (new RecordCollection($this->_mapper, [$this->_mapper])),
      $this->_listColumns
    );
    return new RenderGroup($this->mapperNav(), $tbl);
  }

  public function renderNew()
  {
    $form = new FortifyForm($this->_mapper, $this->baseUri());

    return new RenderGroup(
      $this->mapperNav('/', 'Show List'),
      $this->getAlert(),
      $form
    );
  }

  public function renderEdit($id = 0)
  {
    $this->requireJs('addField');
    $this->requireCss('buildCommandModal');

    $form                = new FortifyForm($this->_mapper, $this->baseUri());
    $buildCommands       = BuildsCommands::collection(['build_id' => $id]);
    $buildCommandsView   = $this->createView(
      new BuildCommands($buildCommands)
    );
    $commands            = Command::collection()->loadAll()->getKeyPair(
      'id',
      'name'
    );
    $addCommandModalForm = new AddBuildCommandsForm($id, $commands);

    return new RenderGroup(
      $this->mapperNav(),
      $form,
      $addCommandModalForm,
      $buildCommandsView
    );
  }
}
