<?php
/**
 * Author: oke.ugwu
 * Date: 16/07/13 16:41
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\TemplatedViewModel;

class PackagesList extends TemplatedViewModel
{
  public $packages;
  public $heading;

  public function __construct($packages, $heading)
  {
    $this->packages = $packages;
    $this->heading  = $heading;
  }
}
