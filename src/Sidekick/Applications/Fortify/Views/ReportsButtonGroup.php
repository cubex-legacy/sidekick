<?php
/**
 * Author: oke.ugwu
 * Date: 20/06/13 17:55
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\HtmlElement;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class ReportsButtonGroup extends ViewModel
{

  public function render()
  {
    $basePath = $this->request()->path(4);
    $partial  = new Partial('<a class="btn" href="%s">%s</a>');

    $reportsGroups = [
      $basePath . '/phploc'  => 'PHP LOC',
      $basePath . '/phpmd'   => 'PHP Mess Detection',
      $basePath . '/phpcs'   => 'Style Check Warning',
      $basePath . '/phpunit' => 'PHP Unit'
    ];

    foreach($reportsGroups as $href => $name)
    {
      $partial->addElement($href, $name);
    }

    $backButton = new HtmlElement(
      'a',
      [
      'href'  => $this->request()->Path(4),
      'class' => 'btn btn-info pull-right',
      'style' => 'margin-left:10px;'
      ],
      'Back to Build Home'
    );

    $backButton = new HtmlElement(
      'div',
      ['class' => 'cushion pull-right'],
      $backButton
    );

    $reportButtonGroup = new HtmlElement(
      'div',
      ['class' => 'btn-group cushion pull-right'],
      $partial
    );

    return new RenderGroup($backButton, $reportButtonGroup);
  }
}
