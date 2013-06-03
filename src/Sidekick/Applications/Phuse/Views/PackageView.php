<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\TemplatedViewModel;

class PackageView extends TemplatedViewModel
{
  protected $_package;
  protected $_releases;

  public function __construct($package, $releases)
  {
    $this->_package = $package;
    $this->_releases = $releases;
  }

  public function getPackage()
  {
    return $this->_package;
  }

  public function getReleases()
  {
    return $this->_releases;
  }
}
