<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Sidekick\Applications\BaseApp\Controllers\ProjectAwareBaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;

class DiffuseController extends ProjectAwareBaseControl
{
  protected $_titlePrefix = 'Diffuse';

  public function preRender()
  {
    parent::preRender();
    $this->requireCss('diffuse');
  }

  public function getSidebar()
  {
    return new Sidebar(
      $this->request()->path(3),
      [
      $this->appBaseUri() . ''           => 'Available Versions',
      $this->appBaseUri() . '/platforms' => 'Manage Platforms',
      $this->appBaseUri() . '/hosts'     => 'Manage Hosts'
      ]
    );
  }
}
