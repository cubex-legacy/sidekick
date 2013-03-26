<?php
/**
 * @author: brooke.bryan
 *        Application: BaseApp
 */
namespace Sidekick\Applications\BaseApp\Controllers;

use Cubex\Core\Controllers\WebpageController;
use Sidekick\Applications\BaseApp\Views\Sidebar;

class BaseControl extends WebpageController
{
  public function preRender()
  {
    $this->requireCssLibrary("bootstrap");
    $this->nest("sidebar", $this->getSidebar());
  }

  public function getSidebar()
  {
    $project = $this->application()->project();
    /**
     * @var $project \Sidekick\Project
     */
    return new Sidebar($project);
  }

  public function renderIndex()
  {
    echo "Hey";
  }

  public function defaultAction()
  {
    return "index";
  }
}
