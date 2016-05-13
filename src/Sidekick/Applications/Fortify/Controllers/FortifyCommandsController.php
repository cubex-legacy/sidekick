<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 07/06/13
 * Time: 10:29
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Views\MappersTable;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\Fortify\Views\CommandExample;
use Sidekick\Applications\Fortify\Views\FortifyCommandForm;
use Sidekick\Components\Fortify\Mappers\Command;

class FortifyCommandsController extends FortifyCrudController
{
  protected $_title = 'Command';
  protected $_perPage = 20;

  public function __construct()
  {
    parent::__construct(
      new Command(),
      ['id', 'name', 'command']
    );
  }

  public function getSidebar()
  {
    return new Sidebar(
      $this->request()->path(3),
      [
        $this->appBaseUri() . '/build-configs/builds'     => 'Builds',
        $this->appBaseUri() . '/build-configs/commands' => 'Commands',
        $this->appBaseUri() . '/build-configs/projects' => 'Project Builds',
      ]
    );
  }

  public function renderShow($id = 0)
  {
    $this->_mapper->load($id);
    $example = new CommandExample($this->_mapper, false);
    $tbl     = new MappersTable(
      $this->baseUri(),
      (new RecordCollection($this->_mapper, [$this->_mapper])),
      $this->_listColumns
    );
    return new RenderGroup($this->mapperNav(), $example, $tbl);
  }

  public function renderNew()
  {
    $this->setBaseUri($this->baseUri());
    $view = $this->createView(new FortifyCommandForm($this->_mapper));

    return new RenderGroup(
      '<h1>New ' . $this->_title . '</h1>',
      $this->getAlert(),
      $view
    );
  }

  public function renderEdit($id = 0)
  {
    $this->requireJs('addField');
    $this->_mapper->load($id);

    $this->setBaseUri($this->baseUri());
    $view    = $this->createView(new FortifyCommandForm($this->_mapper));
    $example = new CommandExample($this->_mapper, true);

    return new RenderGroup(
      '<h1>Edit '.$this->_title.'</h1>',
      $example,
      $view
    );
  }
}
