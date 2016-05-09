<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 27/05/13
 * Time: 14:13
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\BaseApp\Views;

use Cubex\Core\Application\Application;
use Cubex\Facade\Auth;
use Cubex\Facade\Session;
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
    $navItems = new Partial(
      '<li class="%s"><a %s class="%s" href="%s" title="%s">%s</a>%s</li>',
      null, false
    );

    $navItems->addElement(
      '',
      '',
      '',
      '/projects',
      'New menu',
      'Projects',
      ''
    );

    $navItems->addElement(
      '',
      '',
      '',
      '/',
      'New menu',
      'Builds',
      ''
    );

    $navItems->addElement(
      '',
      '',
      '',
      '/fortify/build-configs',
      'New menu',
      'Build Configs',
      ''
    );

    $navItems->addElement(
      '',
      '',
      '',
      '/diffuse/deployments',
      'New menu',
      'Deployments',
      ''
    );

    $navItems->addElement(
      '',
      '',
      '',
      '/diffuse/hosts',
      'New menu',
      'Deployment Configs',
      ''
    );

    $navItems->addElement(
      '',
      '',
      '',
      '/phuse',
      'New menu',
      'Phuse',
      ''
    );
    $navItems->addElement(
      '',
      '',
      '',
      '/users',
      'New menu',
      'Users',
      ''
    );

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
      <li><a href="/overview/logout">Logout</a></li>
    </ul>
  </div>'
    );
  }

}
