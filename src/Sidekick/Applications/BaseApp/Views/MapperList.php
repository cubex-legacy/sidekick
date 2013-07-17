<?php
/**
 * Author: oke.ugwu
 * Date: 17/07/13 12:11
 */

namespace Sidekick\Applications\BaseApp\Views;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Helpers\Paginator;

class MapperList extends TemplatedViewModel
{
  public function pager($page, $count, $perPage, $baseUri = null)
  {
    if(null === $baseUri)
    {
      $baseUri = $this->request()->path(2) . '/page/';
    }

    $pagination = new Paginator();
    $pagination->setNumResults($count);
    $pagination->setNumResultsPerPage($perPage);
    $pagination->setPage($page);
    $pagination->setUri($baseUri);
    return $pagination;
  }

  public function filters($items)
  {
    $filters = new RenderGroup();

    if(is_assoc($items))
    {
      foreach($items as $item => $href)
      {
        $filter = new HtmlElement(
          'a',
          [
          'href'  => $href,
          'class' => "pull-right cushion"
          ],
          ucfirst($item)
        );

        $filters->add($filter);
      }
    }
    else
    {
      foreach($items as $item)
      {
        $filter = new HtmlElement(
          'a',
          [
          'href'  => $this->baseUri() . '/' . $item,
          'class' => "pull-right cushion"
          ],
          ucfirst($item)
        );

        $filters->add($filter);
      }
    }

    $filter = new HtmlElement(
      'a',
      [
      'href'  => $this->baseUri() . '/all',
      'class' => "pull-right cushion"
      ],
      'Show All'
    );

    $filters->add($filter);
    return $filters;
  }
}
