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
    return null;
  }
}
