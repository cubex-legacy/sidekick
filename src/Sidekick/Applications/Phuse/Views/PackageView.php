<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\TemplatedViewModel;

class PackageView extends TemplatedViewModel
{
  public $package;
  public $releases;

  public function __construct($package, $releases)
  {
    $this->package = $package;
    $this->releases = $releases;
  }
}
