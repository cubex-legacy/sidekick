<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 15/05/13
 * Time: 09:47
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\ViewModel;

class Breadcrumbs extends ViewModel
{
  protected $_structure = [];

  public function addItem($itemName, $itemUrl = null)
  {
    $this->_structure[] = [$itemName, $itemUrl];
  }

  public function render()
  {
    /**
     * @var structure
     * Purely used to know what point in the array we are in
     */
    $structure = $this->_structure;

    $breadcrumbs = '<ul class="breadcrumb">';
    foreach($this->_structure as $item)
    {
      $breadcrumbs .= '<li>';
      if($item[1] === null)
      {
        $breadcrumbs .= $item[0];
      }
      else
      {
        $breadcrumbs .= '<a href="' . $item[1] . '">';
        $breadcrumbs .= $item[0];
        $breadcrumbs .= '</a>';
      }
      if(next($structure))
      {
        $breadcrumbs .= '<span class="divider">/</span>';
      }
      $breadcrumbs .= '</li>';
    }

    $breadcrumbs .= '</ul>';
    return $breadcrumbs;
  }
}