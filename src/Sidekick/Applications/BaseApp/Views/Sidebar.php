<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\BaseApp\Views;

use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class Sidebar extends ViewModel
{
  public function __construct($path, $items = [])
  {
    $this->_path  = $path;
    $this->_items = empty($items) ?
    ['overview/releases' => 'Recent Releases'] :
    $items;
  }

  public function render()
  {
    $navItems = new Partial(
      '<li class="%s"><a href="%s">%s</a></li>'
    );

    foreach($this->_items as $appPath => $name)
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
