<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 10:29
 */

namespace Sidekick\Applications\Diffuse\Views\Hosts;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\Build;

class HostsIndex extends TemplatedViewModel
{
  protected $_hosts;

  public function __construct($hosts)
  {
    $this->_hosts = $hosts;
  }

  public function getHosts()
  {
    return $this->_hosts;
  }

}
