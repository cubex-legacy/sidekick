<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\BaseApp\Views\Sidebar;

class DiffuseController extends BaseControl
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
        $this->appBaseUri() . '/manage-hosts'     => 'Manage Hosts',
        $this->appBaseUri() . '/manage-configs' => 'Manage Configuration',
      ]
    );
  }
}
