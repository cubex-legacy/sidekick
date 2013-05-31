<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 31/05/13
 * Time: 12:44
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\TemplatedViewModel;

class PackageResults extends TemplatedViewModel
{
  public $packages;

  public function __construct($packages)
  {
    $this->packages = $packages;
  }
}
