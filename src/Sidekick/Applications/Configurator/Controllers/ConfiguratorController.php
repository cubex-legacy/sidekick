<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Controllers;

use Cubex\View\Impart;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Configurator\Views\Sidebar;

class ConfiguratorController extends BaseControl
{
  protected $_titlePrefix = 'Configurator';

  public function preRender()
  {
    parent::preRender();
    $this->requireCss('base');
    $this->requireJs('environment');
    $this->requireJs('project');

    $this->nest('sidebar', new Sidebar());
  }
}
