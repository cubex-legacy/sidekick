<?php
/**
 * Author: oke.ugwu
 * Date: 11/06/13 18:08
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\Helpers\Inflection;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class FortifyMapperList extends ViewModel
{
  public function __construct(
    $listTitle, $mapperTable, $paginator, $baseUri, $alert = ''
  )
  {
    $this->mapperTable = $mapperTable;
    $this->paginator   = $paginator;
    $this->alert       = $alert;
    $this->baseUri     = $baseUri;
    $this->listTitle   = $listTitle;
  }

  public function render()
  {
    $newButton = new Partial(
      '<a href="%s" class="btn btn-success pull-right" style="margin-top:-20px;">
        <i class="icon-plus icon-white"></i>%s
      </a>'
    );

    $newButton->addElement(
      $this->baseUri . '/new',
      'New ' . $this->listTitle
    );

    return new RenderGroup(
      '<h1>' . Inflection::pluralise($this->listTitle) . '</h1>',
      $newButton,
      '<div class="clearfix" style="margin-top:20px;"></div>',
      $this->alert,
      $this->mapperTable,
      $this->paginator->getPager()
    );
  }
}
