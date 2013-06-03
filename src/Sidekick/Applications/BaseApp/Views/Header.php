<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 27/05/13
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\BaseApp\Views;

use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Project;

class Header extends ViewModel
{
  protected $_project;

  public function __construct(Project $project)
  {
    $this->_project = $project;
  }

  public function render()
  {
    $navItems = new Partial(
      '<li class="%s">
        <a data-animation="true" data-placement="bottom" href="%s" title="%s">
          %s
        </a>
      </li>'
    );

    $apps = $this->_project->getApplications();
    $path = $this->request()->path();
    foreach($apps as $appPath => $app)
    {
      //active if path starts with appPath
      $state = starts_with($path, "/$appPath", false) ? 'active' : '';
      $navItems->addElement(
        $state,
        ('/' . $appPath),
        $app->description(),
        $app->name()
      );
    }

    return new RenderGroup(
      '<a class="brand" href="/" style="width:190px;padding:0 0 0 16px;">',
      '<img style="margin-top: 10px; height:25px;" src="',
      $this->imgUrl('/logo.png'), '"/>',
      '</a>',
      '<ul class="nav">',
      $navItems,
      '</ul>',
      '<div class="nav-collapse collapse">
        <ul class="nav pull-right">
          <li><a href="/profile">John Doe</a></li>
          <li><a href="/logout">Logout</a></li>
        </ul>
      </div>'
    );
  }
}
