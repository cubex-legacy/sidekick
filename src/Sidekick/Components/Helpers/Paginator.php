<?php

namespace Sidekick\Components\Helpers;

/**
 * @author gareth.evans
 */

use Cubex\I18n\ITranslatable;
use Cubex\I18n\TranslateTraits;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;

class Paginator implements ITranslatable
{
  use TranslateTraits;

  protected $_page = 1;
  protected $_pages = 1;
  protected $_numPagesBefore = 3;
  protected $_numPagesAfter = 4;
  protected $_numResults = 0;
  protected $_numResultsPerPage = 20;
  protected $_uri = "/";
  protected $_nextLabel = '&rarr;';
  protected $_prevLabel = '&larr;';
  protected $_paginateCalled = false;

  public function paginate($rePaginate = false)
  {
    if(!$this->_paginateCalled || $rePaginate)
    {
      $this->_paginateCalled = true;

      $this->_pages = ceil($this->_numResults / $this->_numResultsPerPage);

      if($this->getPage() > $this->_pages)
      {
        $this->setPage($this->_pages);
      }
    }
    return $this;
  }

  public function __toString()
  {
    return (string)$this->getPager();
  }

  public function getPager()
  {
    $this->paginate();

    $edges = new Partial('<li class="%s">%s</li>', null, false);
    $pages = new Partial('<li class="%s"><a href="%s">%s</a></li>');

    $pager = new RenderGroup();
    $pager->add(
      <<<HTML
      <div class="pagination pagination-centered">
  <ul>
HTML
    );

    if(1 === $this->getPage())
    {
      $edges->addElement("disabled", '<span>' . $this->_prevLabel . '</span>');
    }
    else
    {
      $url = $this->_getUrl($this->getPage() - 1);
      $edges->addElement(
        "",
        ('<a href="' . $url . '">' . $this->_prevLabel . '</a>')
      );
    }

    $pager->add($edges->render());
    $edges->clearElements();
    for($page = 1; $page <= $this->_pages; $page++)
    {
      if($page < ($this->getPage() - $this->_numPagesBefore))
      {
        if($page == 1)
        {
          $pages->addElement("", $this->_getUrl(1), "First");
        }
        continue;
      }
      else if($page > ($this->getPage() + $this->_numPagesAfter))
      {
        $pages->addElement("", $this->_getUrl($this->_pages), "Last");
        break;
      }
      else
      {
        $class = $page == $this->getPage() ? "active" : "";
        $pages->addElement($class, $this->_getUrl($page), $page);
      }
    }

    $pager->add($pages);
    if($this->_pages == $this->getPage() || $this->_pages <= 1)
    {
      $edges->addElement("disabled", '<span>' . $this->_nextLabel . '</span>');
    }
    else
    {
      $url = $this->_getUrl($this->getPage() + 1);
      $edges->addElement(
        "",
        ('<a href="' . $url . '">' . $this->_nextLabel . '</a>')
      );
    }

    $pager->add($edges->render());
    $edges->clearElements();

    $pager->add(
      <<<HTML
  </ul>
</div>
HTML
    );

    if($this->_pages > 1)
    {
      return $pager;
    }
    return '';
  }

  public function getInfo()
  {
    $this->paginate();

    $maxResult = $this->getPage() * $this->_numResultsPerPage;
    $minResult = ($maxResult + 1) - $this->_numResultsPerPage;

    if($maxResult > $this->_numResults)
    {
      $maxResult = $this->_numResults;
    }

    $info = "Showing {$minResult} to {$maxResult} of ";
    $info .= $this->tp("%d result(s)", $this->_numResults);

    return $info;
  }

  protected function _getUrl($page)
  {
    $url = $this->_uri;
    $url = rtrim($url, "/") . "/P{$page}";

    return $url;
  }

  public function setPage($page)
  {
    $this->_page = (int)$page >= 1 ? (int)$page : 1;

    return $this;
  }

  public function getPage()
  {
    return $this->_page;
  }

  public function setNumResults($numResults)
  {
    $this->_numResults = $numResults;

    return $this;
  }

  public function setNumResultsPerPage($numResultsPerPage)
  {
    $this->_numResultsPerPage = $numResultsPerPage;

    return $this;
  }

  public function setUri($uri)
  {
    $this->_uri = $uri;

    return $this;
  }

  public function setNextLabel($label)
  {
    $this->_nextLabel = $label;

    return $this;
  }

  public function setPrevLabel($label)
  {
    $this->_prevLabel = $label;

    return $this;
  }

  public function setNumPagesBefore($numPagesBefore)
  {
    $this->_numPagesBefore = $numPagesBefore;
  }

  public function setNumPagesAfter($numPagesAfter)
  {
    $this->_numPagesAfter = $numPagesAfter;
  }

  public function getOffset()
  {
    return $this->_page > 1 ? ($this->_page - 1) * $this->_numResultsPerPage : 0;
  }
}
