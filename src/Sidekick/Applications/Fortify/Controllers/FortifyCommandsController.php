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
use Sidekick\Applications\Fortify\Views\CommandExample;
use Sidekick\Applications\Fortify\Views\FortifyCommandForm;
use Sidekick\Components\Fortify\Mappers\Command;

class FortifyCommandsController extends FortifyCrudController
{
  public function __construct()
  {
    parent::__construct(
      new Command(),
      ['id', 'name', 'command']
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

  public function setErrors($errors)
  {
    $this->_errors = $errors;
  }

  public function getErrors()
  {
    return $this->_errors;
  }

  public function renderNew()
  {
    $form = new FortifyCommandForm($this->_mapper, $this->baseUri());

    return new RenderGroup(
      $this->mapperNav('/', 'Show List'),
      $this->getAlert(),
      $form
    );
  }

  public function renderEdit($id = 0)
  {
    $this->requireJsLibrary('jquery');
    $this->requireJs('addField');

    $this->_mapper->load($id);

    $form = new CommandExample($this->_mapper, true);
    $form .= new FortifyCommandForm($this->_mapper, $this->baseUri());

    return new RenderGroup(
      $this->mapperNav(),
      $form
    );
  }
}
