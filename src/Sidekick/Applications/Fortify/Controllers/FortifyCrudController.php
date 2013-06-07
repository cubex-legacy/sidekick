<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Data\Attribute\Attribute;
use Cubex\Form\Form;
use Cubex\Helpers\Strings;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\MapperController;
use Sidekick\Applications\BaseApp\Views\MappersTable;
use Sidekick\Applications\BaseApp\Views\Sidebar;
use Sidekick\Applications\BaseApp\Views\Alert;
use Sidekick\Applications\Fortify\Views\AddBuildCommandsForm;
use Sidekick\Applications\Fortify\Views\BuildCommands;
use Sidekick\Applications\Fortify\Views\CommandExample;
use Sidekick\Applications\Fortify\Views\FortifyCommandForm;
use Sidekick\Applications\Fortify\Views\FortifyForm;
use Sidekick\Components\Fortify\Mappers\BuildsCommands;
use Sidekick\Components\Fortify\Mappers\Command;

class FortifyCrudController extends MapperController
{

  protected $_errors;

  public function preRender()
  {
    parent::preRender();
    $this->requireCss('fortifyForms');
  }

  public function getSidebar()
  {
    return new Sidebar(
      $this->request()->path(2),
      ['/fortify/builds' => 'Builds', '/fortify/commands' => 'Commands']
    );
  }

  protected function _saveMapper()
  {
    $d = new DebuggerBundle();
    $d->init();
    $result = false;
    $post   = $this->postVariables();
    foreach($post as $attr => $value)
    {
      if(is_array($value))
      {
        foreach($value as $k => $val)
        {
          if(trim($val) == '')
          {
            unset($value[$k]);
          }
        }
        $post[$attr] = $value;
      }
    }
    $this->_mapper->hydrate($post);
    if($this->_mapper instanceof Command)
    {
      if(!$this->postVariables('cause_build_failure'))
      {
        $this->_mapper->causeBuildFailure = false;
      }
      if(!$this->postVariables('run_on_file_set'))
      {
        $this->_mapper->runOnFileSet = false;
      }
    }

    try
    {
      $result = $this->_mapper->saveChanges(true);
      if($result !== true)
      {
        $this->setErrors($this->_mapper->validationErrors());
      }
    }
    catch(\Exception $e)
    {
      $this->setErrors($this->_mapper->validationErrors());
    }
    return $result;
  }

  public function renderShow($id = 0)
  {
    $this->_mapper->load($id);
    $example = '';
    if($this->_mapper instanceof Command)
    {
      $example = new CommandExample($this->_mapper, false);
    }
    $tbl = new MappersTable(
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
    if($this->_mapper instanceof Command)
    {
      $form = new FortifyCommandForm($this->_mapper, $this->baseUri());
    }
    else
    {
      $form = new FortifyForm($this->_mapper, $this->baseUri());
    }
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

    $buildCommandsView = $addCommandModalForm = '';
    $this->_mapper->load($id);
    if($this->_mapper instanceof Command)
    {
      $form = new CommandExample($this->_mapper, true);
      $form .= new FortifyCommandForm($this->_mapper, $this->baseUri());
    }
    else
    {
      $form                = new FortifyForm($this->_mapper, $this->baseUri());
      $buildCommands       = BuildsCommands::collection(['build_id' => $id]);
      $buildCommandsView   = $this->createView(
        new BuildCommands($buildCommands)
      );
      $commands    = Command::collection()->loadAll()->getKeyPair('id', 'name');
      $addCommandModalForm = new AddBuildCommandsForm($id, $commands);
    }

    return new RenderGroup(
      $this->mapperNav(),
      $form,
      $addCommandModalForm,
      $buildCommandsView
    );
  }

  public function renderCreate()
  {
    $result = $this->_saveMapper();
    if($result === true)
    {
      $id = $this->_mapper->id();
      \Redirect::to($this->baseUri() . '/' . $id)->now();
    }
    else
    {
      \Redirect::to($this->baseUri() . '/new')->with(
        'msg',
        ['type' => Alert::TYPE_ERROR, 'msg' => $this->getErrors()]
      )->now();
    }
  }

  public function renderUpdate($id = 0)
  {
    $this->_mapper->load($id);
    $result = $this->_saveMapper();
    if($result === true)
    {
      \Redirect::to($this->baseUri() . '/' . $id)->now();
    }
    else
    {
      \Redirect::to($this->baseUri() . '/' . $id . 'edit')->with(
        'msg',
        ['type' => Alert::TYPE_ERROR, 'msg' => $this->getErrors()]
      )->now();
    }
  }

  public function renderDestroy($id = 0)
  {
    $this->_mapper->load($id);
    $this->_mapper->delete();
    \Redirect::to($this->baseUri())->with(
      'msg',
      ['type' => Alert::TYPE_SUCCESS, 'msg' => 'Item Deleted']
    )->now();
  }
}
