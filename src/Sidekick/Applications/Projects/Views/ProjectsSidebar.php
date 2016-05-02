<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 30/05/13
 * Time: 14:07
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Projects\Views;

use Cubex\View\ViewModel;
use Cubex\View\Impart;
use Cubex\View\RenderGroup;

class ProjectsSidebar extends ViewModel
{
  protected $_appBaseUri;

  public function __construct($appBaseUri)
  {
    $this->_appBaseUri = $appBaseUri;
  }

  public function render()
  {
    $path      = $this->request()->path();
    $menuItems = [
      'All Projects'   => $this->_appBaseUri.'/',
      'Create Project' => $this->_appBaseUri . '/create-project',
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
      $str .= '<a href="' . $href . '">' . $label . '</a>';
      $str .= '</li>';
    }
    $str .= '</ul></div>';

    return new Impart($str);
  }
}
