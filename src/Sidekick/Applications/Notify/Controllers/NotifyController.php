<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Controllers;

use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use \Sidekick\Applications\BaseApp\Views\Sidebar;

class NotifyController extends BaseControl
{

  public function renderIndex()
  {
    //Notify::trigger("PAGE_LOAD");
    return "<h1>Welcome to Notify</h1>";
  }

  public function getSidebar()
  {
    $sidebarMenu = [
      "/notify" => "Home",
      "/notify/events" => "Manage Event Types",
      "/notify/hooks" => "My Notifications",
      "/notify/groups" => "Notification Groups"
    ];
    return new RenderGroup(
      new Sidebar($this->request()->path(2), $sidebarMenu)
    );
  }

  public function getRoutes()
  {
    return [

    ];
  }
}
