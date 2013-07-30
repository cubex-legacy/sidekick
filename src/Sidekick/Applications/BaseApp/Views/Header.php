<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 27/05/13
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\BaseApp\Views;

use Cubex\Facade\Auth;
use Cubex\View\Impart;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Components\Users\Mappers\User;
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
    $structure = [];

    $apps = $this->_project->getApplications();
    foreach($apps as $appPath => $app)
    {
      $group = $app->getNavGroup();
      if($group !== null)
      {
        $structure[$group][$appPath] = $app;
      }
      else
      {
        $structure[$app->name()][$appPath] = $app;
      }
    }

    $navItems = new Partial(
      '<li class="%s">
        <a %s class="%s" href="%s" title="%s">%s</a>%s</li>'
      , null, false);

    $subNavItems = new Partial(
      '<li class="%s">
        <a data-animation="true" data-placement="bottom" href="%s" title="%s">
          %s
        </a>
      </li>'
    );

    $path = $this->request()->path();

    foreach($structure as $group => $apps)
    {
      if(count($apps) > 1)
      {
        foreach($apps as $appPath => $app)
        {
          //active if path starts with appPath
          $state = $this->_getSubNavState($path, $appPath);
          $subNavItems->addElement(
            $state,
            ('/' . $appPath),
            $app->description(),
            $app->name()
          );
        }

        $state = $this->_getMainNavState($path, $structure[$group]);
        $navItems->addElement(
          'dropdown' . ' ' . $state,
          'data-toggle="dropdown" data-hover="dropdown"',
          'dropdown-toggle',
          ('/' . $appPath),
          '',
        $group . ' <b class="caret"></b>',
        '<ul class="dropdown-menu">' . $subNavItems->render() . '</ul>'
        );

        $subNavItems->clearElements();
      }
      else
      {
        $state   = $this->_getMainNavState($path, $structure[$group]);
        $appPath = key($apps);
        $navItems->addElement(
          $state,
          '',
          '',
        '/' . $appPath,
          $apps[$appPath]->description(),
          $group,
          ''
        );
      }
    }

    return new RenderGroup(
      '<a id="sidekick-logo" class="brand" href="/">',
      ('<img src="' . $this->imgUrl('/logo.png') . '"/></a>'),
      '<ul class="nav">',
      $navItems,
      '</ul>',
      '<div class="nav-collapse collapse">
        <ul class="nav pull-right">
          <li><a href="/profile">',
      Auth::user()->getDetail("display_name", \Auth::getRawUsername()),
      '</a></li>
      <li><a href="/logout">Logout</a></li>
    </ul>
  </div>'
    );
  }

  private function _getMainNavState($path, $navGroup)
  {
    $currentApp = explode('/', $path)[1];
    $state      = '';
    if(array_key_exists($currentApp, $navGroup))
    {
      $state = 'active';
    }

    return $state;
  }

  /**
   * @param $path
   * @param $appPath
   *
   * @return string
   */
  private function _getSubNavState($path, $appPath)
  {
    $state = starts_with($path, "/$appPath", false) ? ' active' : '';
    return $state;
  }
}
