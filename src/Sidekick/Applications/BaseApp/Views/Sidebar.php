<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\BaseApp\Views;

use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Project;

class Sidebar extends ViewModel
{
  public function __construct()
  {
  }

  public function render()
  {
    $navItems = new Partial(
      '<li><a href="%s">%s</a></li>'
    );

    $apps = ['overview/releases' => 'Recent Releases'];
    foreach($apps as $appPath => $name)
    {
      $navItems->addElement('/' . $appPath, $name);
    }

    return new RenderGroup(
      '<div class="tabbable tabs-left"><ul class="nav nav-tabs">',
      $navItems,
      '</ul></div>'
    );
  }
}
