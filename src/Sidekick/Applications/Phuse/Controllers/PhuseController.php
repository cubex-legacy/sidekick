<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;

class PhuseController extends BaseControl
{
  protected $_titlePrefix = 'Phuse';

  public function preRender()
  {
    parent::preRender();
    $this->requireCss('base');
  }
}
