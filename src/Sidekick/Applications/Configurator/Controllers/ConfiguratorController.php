<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Controllers;

use Cubex\View\Impart;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;

class ConfiguratorController extends BaseControl
{
  public function preRender()
  {
    parent::preRender();
    $this->requireCss('base');

    $path      = $this->request()->path();
    $menuItems = [
      'Projects'     => '/configurator',
      'Environments' => '/configurator/environments',
      'Settings'     => '/configurator/settings'
    ];

    $str = "<ul class='nav nav-tabs'>";
    foreach($menuItems as $label => $href)
    {
      if($href == $path)
      {
        $str .= "<li class='active'>";
      }
      else
      {
        $str .= "<li>";
      }
      $str .= "<a href='$href'>$label</a>";
      $str .= "</li>";
    }
    $str .= "</ul>";

    $this->nest('tabnav', new Impart($str));
  }
}