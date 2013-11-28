<?php
/**
 * Author: oke.ugwu
 * Date: 23/10/13 14:00
 */

namespace Sidekick\Applications\Evento\Views;

use Cubex\View\ViewModel;
use Cubex\View\Impart;
use Cubex\View\RenderGroup;

class EventoSidebar extends ViewModel
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
      'All Events'          => $this->_appBaseUri,
      'Open Events'         => $this->_appBaseUri . '/open',
      'Event Types'         => $this->_appBaseUri . '/types',
      'Subscribe to Events' => $this->_appBaseUri . '/subscribe',
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
