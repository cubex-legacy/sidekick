<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 10:29
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\TemplatedViewModel;

class PlatformIndex extends TemplatedViewModel
{
  protected $_platforms;

  public function __construct($platforms)
  {
    $this->_platforms = $platforms;
  }

  public function getPlatforms()
  {
    return $this->_platforms;
  }
}
