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
  protected $_project;

  public function __construct(Project $project)
  {
    $this->_project = $project;
  }

  public function render()
  {
    $navItems = new Partial(
      '<li><a href="%s"><i class="icon-chevron-right"></i>' .
      '%s<br/><small>%s</small></a></li>'
    );

    $apps = $this->_project->getApplications();
    foreach($apps as $appPath => $app)
    {
      $navItems->addElement('/' . $appPath, $app->name(), $app->description());
    }

    return new RenderGroup(
      '<ul class="nav nav-list bs-docs-sidenav affix-top">',
      $navItems,
      '</ul>'
    );
  }
}
