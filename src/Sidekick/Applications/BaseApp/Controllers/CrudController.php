<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\BaseApp\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Cubex\Form\Form;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Mapper\Database\RecordMapper;
use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\Text\TextTable;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;

class CrudController extends WebpageController
{
  protected $_mapper;

  public function __construct(RecordMapper $mapper, $listColumns = null)
  {
    $this->_mapper      = $mapper;
    $this->_listColumns = $listColumns;
  }

  public function runHeader()
  {
    return new RenderGroup(
      new HtmlElement("a", ['href' => $this->baseUri()], 'Show List'),
      " | ",
      new HtmlElement("a", ['href' => $this->baseUri() . '/new'], 'Create New'),
      "<hr/>"
    );
  }

  public function renderNew()
  {
    echo $this->runHeader();
    echo (new Form("CrudForm", $this->baseUri()))->bindMapper($this->_mapper);
  }

  public function renderShow($id = 0)
  {
    echo $this->runHeader();
    $this->_mapper->load($id);
    echo '<pre>';
    echo TextTable::fromArray([$this->_mapper]);
    echo '</pre>';
    echo new HtmlElement("a", ['href' => $id . '/edit'], 'Edit');

    $delForm = new Form("DeleteForm", $this->baseUri() . '/' . $id . '/delete');
    $delForm->addSubmitElement("Delete");
    echo $delForm;
  }

  public function renderEdit($id = 0)
  {
    $this->_mapper->load($id);
    echo (new Form("CrudForm", $this->baseUri() . '/' . $id))
    ->bindMapper($this->_mapper);
  }

  public function renderUpdate($id = 0)
  {
    $this->_mapper->load($id);
    $this->_mapper->hydrate($this->postVariables());
    $this->_mapper->saveChanges();
    \Redirect::to($this->baseUri() . '/' . $id)->now();
  }

  public function renderDestroy($id = 0)
  {
    $this->_mapper->load($id);
    $this->_mapper->delete();
    \Redirect::to($this->baseUri())->with("status", "Item deleted")->now();
  }

  public function renderCreate()
  {
    $this->_mapper->hydrate($this->postVariables());
    $this->_mapper->saveChanges();
    $id = $this->_mapper->id();
    \Redirect::to($this->baseUri() . '/' . $id)->now();
  }

  public function renderIndex()
  {
    echo $this->runHeader();
    $collection = new RecordCollection($this->_mapper);
    $collection->loadAll();
    echo '<pre>';

    $data = [];
    if($this->_listColumns === null)
    {
      $raw = $collection->jsonSerialize();
    }
    else
    {
      $raw = $collection->getKeyedArray("id", $this->_listColumns);
    }
    foreach($raw as $row)
    {
      $row['id'] = (new HtmlElement(
        "a", ['href' => $this->baseUri() . '/' . $row['id']], $row['id']
      ))->render();
      $data[]    = $row;
    }

    echo TextTable::fromArray($data);
    echo '</pre>';
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
