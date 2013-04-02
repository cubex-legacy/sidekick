<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class Sidebar extends ViewModel
{
  public function render()
  {
    $renderGroup  = [];
    $projectTypes = ['pending', 'recent'];

    /*$navItems = new Partial(
      '<li><a href="%s"><i class="icon-chevron-right"></i>' .
      '%s<br/><small>%s</small></a></li>'
    );*/

    foreach($projectTypes as $ptype)
    {
      $section  = '<h5>' . ucwords($ptype) . ' Projects</h5>';
      $projects = ['Wilma', 'Support', 'Login', 'Signup'];
      $section .= '<ul class="nav nav-list bs-docs-sidenav affix-top">';

      foreach($projects as $p)
      {
        $section .= '<li><a href="/diffuse/projects/' . $p . '">' . $p . '</a></li>';
      }

      $section .= '</ul>';

      $renderGroup[] = $section;
    }

    return new RenderGroup($renderGroup);
  }
}
