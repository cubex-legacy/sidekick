<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Fortify\Controllers;

use Cubex\Routing\Templates\ResourceTemplate;
use Cubex\View\Partial;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;

class FortifyController extends BaseControl
{

  public function getSidebar()
  {
    return new Sidebar(
      $this->request()->path(2),
      ['/fortify/builds' => 'Builds', '/fortify/commands' => 'Commands']
    );
  }
  public function renderIndex()
  {
    $p = new Partial('<div><a class="btn span3" href="%s">%s</a></div>');
    $p->addElement('/fortify/builds', 'Builds');
    $p->addElement('/fortify/commands', 'Commands');
    return '<h3 class="span12">Code Build and Testing</h3><br />'.$p;
  }

  public function getRoutes()
  {
    return ResourceTemplate::getRoutes();
  }
}
