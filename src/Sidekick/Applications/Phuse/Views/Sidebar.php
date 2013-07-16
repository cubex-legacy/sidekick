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
  public function __construct($path)
  {
    $this->_path  = $path;
  }

  public function render()
  {
    $navItems = new Partial(
      '<li class="%s"><a href="%s">%s</a></li>'
    );

    $items = [
      '/phuse' => 'Home',
      '/phuse/new-packages' => 'New Packages',
      '/phuse/recent-releases' => 'Recent Releases',
      '/phuse/all' => 'All Packages'
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
