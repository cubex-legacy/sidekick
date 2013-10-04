<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Phuse\Mappers\Package;

class PackageView extends TemplatedViewModel
{
  protected $_package;
  protected $_releases;

  public function __construct(Package $package, $releases)
  {
    $this->_package = $package;
    $this->_releases = $releases;
  }

  /**
   * @return Package
   */
  public function getPackage()
  {
    return $this->_package;
  }

  public function getReleases()
  {
    return $this->_releases;
  }
}
