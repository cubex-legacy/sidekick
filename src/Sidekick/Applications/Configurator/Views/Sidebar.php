<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 27/05/13
 * Time: 14:39
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\Impart;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class Sidebar extends ViewModel
{

  public function render()
  {
    $path      = $this->request()->path();
    $menuItems = [
      'Projects'     => '/configurator',
      'Environments' => '/configurator/environments'
    ];

    $str = '<div class="tabbable tabs-left">';
    $str .= '<ul class="nav nav-tabs">';
    foreach($menuItems as $label => $href)
    {
      if($href == $path)
      {
        $str .= '<li class="active">';
      }
      else
      {
        $str .= '<li>';
      }
      $str .= '<a href="'.$href.'">'.$label.'</a>';
      $str .= '</li>';
    }
    $str .= '</ul></div>';

    return new Impart($str);
  }
}
