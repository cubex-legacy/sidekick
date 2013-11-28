<?php
/**
 * Author: oke.ugwu
 * Date: 16/07/13 16:01
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class Sidebar extends ViewModel
{
  protected $_path;
  protected $_appBaseUri;

  public function __construct($path, $appBaseUri)
  {
    $this->_path       = $path;
    $this->_appBaseUri = $appBaseUri;
  }

  public function render()
  {
    $navItems = new Partial(
      '<li class="%s"><a href="%s">%s</a></li>'
    );

    $items = [
      $this->_appBaseUri                      => 'Home',
      $this->_appBaseUri . '/new-packages'    => 'New Packages',
      $this->_appBaseUri . '/recent-releases' => 'Recent Releases',
      $this->_appBaseUri . '/all'             => 'All Packages'
    ];

    foreach($items as $appPath => $name)
    {
      $active = $this->_path == $appPath ? 'active' : '';
      $navItems->addElement($active, $appPath, $name);
    }

    return new RenderGroup(
      '<div class="tabbable tabs-left"><ul class="nav nav-tabs">',
      $navItems,
      '</ul></div>'
    );
  }
}
