<?php
/**
 * @author: davide.argellati
 *        Application: BaseApp
 */
namespace Sidekick\Applications\BaseApp\Views;

use Cubex\Mapper\Database\RecordCollection;
use Cubex\Text\TextTable;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Components\Helpers\HtmlTableDecorator;

class MappersTable extends ViewModel
{
  protected $_collection;
  protected $_listColumns;
  protected $_baseUri;

  public function __construct(
    $baseUri,
    RecordCollection $coll,
    $listColumns = null
  )
  {
    $this->_collection  = $coll;
    $this->_listColumns = $listColumns;
    $this->_baseUri     = $baseUri;
  }

  protected function _htmlTableFromCollection()
  {

    $data = [];
    if($this->_listColumns === null)
    {
      $raw = $this->_collection->jsonSerialize();
    }
    else
    {
      $raw = $this->_collection->getKeyedArray("id", $this->_listColumns);
    }
    foreach($raw as $id => $row)
    {

      /* foreach($row as $k => $v)
      {
        $row[$k] = (new HtmlElement(
          "a",
          ['href' => $this->_baseUri . '/' . $id],
          $v
        ))->render();
      }*/
      $row[''] = (new RenderGroup(
        new HtmlElement(
          "a",
          ['href' => $this->_baseUri . '/' . $id . '/edit'],
          '<i class="icon-edit"></i>'
        ),
        new HtmlElement(
          "a",
          [
          'href'    => $this->_baseUri . '/' . $id . '/delete',
          'onclick' => 'return confirm("Are you sure to delete it?");'
          ],
          '<i class="icon-trash"></i>'
        )
      ))->render();

      $data[] = $row;
    }

    $tbl       = TextTable::fromArray($data);
    $decorator = new HtmlTableDecorator();
    $decorator->setStriped(true);
    $decorator->setHover(true);
    $tbl->setDecorator($decorator);
    return (string)$tbl;
  }

  public function render()
  {
    return $this->_htmlTableFromCollection();
  }
}
