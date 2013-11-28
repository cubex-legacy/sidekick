<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\BaseApp\Controllers;

use Cubex\Data\Attribute;
use Cubex\Form\Form;
use Cubex\Helpers\Inflection;
use Cubex\Helpers\Strings;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Mapper\Database\RecordMapper;
use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\Session\CassandraSession\Session;
use Cubex\Text\TextTable;
use Cubex\View\HtmlElement;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Views\MappersTable;
use Sidekick\Applications\BaseApp\Views\Alert;
use Sidekick\Applications\Fortify\Views\FortifyMapperList;
use Sidekick\Components\Helpers\Paginator;

abstract class MapperController extends BaseControl
{
  protected $_mapper;
  protected $_title;
  protected $_listColumns;

  public function __construct(
    RecordMapper $mapper, $listColumns = null
  )
  {
    $this->_mapper      = $mapper;
    $this->_listColumns = $listColumns;
  }

  public function renderIndex($page = 1)
  {
    $collection = new RecordCollection($this->_mapper);
    $collection->loadAll();

    $perpage = 5;
    //cloning because if i count it loads the collection then i can't limit
    $cloneToCount = clone $collection;
    $count        = $cloneToCount->count();
    $paginator    = $this->_getPaginator($page, $count, $perpage);
    $offset       = $paginator->getOffset();
    $collection->setLimit($offset, $perpage);

    $mapperTable = new MappersTable(
      $this->baseUri(), $collection, $this->_listColumns
    );

    return $this->createView(
      new FortifyMapperList(
        $this->_title,
        $mapperTable,
        $paginator,
        $this->getAlert()
      )
    );
  }

  abstract public function renderEdit();

  abstract public function renderUpdate();

  abstract public function renderNew();

  abstract public function renderCreate();

  abstract public function renderDestroy();

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

  protected function _getPaginator(
    $pagenumber,
    $count,
    $perpage,
    $baseUri = null
  )
  {
    if(null === $baseUri)
    {
      $baseUri = $this->baseUri() . '/page/';
    }

    preg_match('!\d+!', $pagenumber, $matches);
    $pagenumber = $matches[0];

    $pagination = new Paginator();
    $pagination->setNumResults($count);
    $pagination->setNumResultsPerPage($perpage);
    $pagination->setPage($pagenumber);
    $pagination->setUri($baseUri);
    return $pagination;
  }

  public function mapperNav($buttons = array())
  {
    if(empty($buttons))
    {
      $buttons['/']    = 'Show List';
      $buttons['/new'] = 'Create New +';
    }
    $partial = new Partial('<a class="btn" href="%s">%s</a>');
    foreach($buttons as $href => $txt)
    {

      $partial->addElement($this->baseUri() . '/' . ltrim($href, '/'), $txt);
    }
    return new RenderGroup(
      new HtmlElement('div', ['class' => "btn-group"], $partial),
      new HtmlElement('hr')
    );
  }

  public function getAlert()
  {
    $alerts  = \Session::getFlash('msg');
    $message = $alerts['msg'];
    if(is_array($alerts['msg']))
    {
      $message = [];
      foreach($alerts['msg'] as $field => $mess)
      {
        $message[] = Strings::humanize($field) . ' : ' . implode(', ', $mess);
      }
      $message = implode(' | ', $message);
    }
    return new Alert($alerts['type'], $message);
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
