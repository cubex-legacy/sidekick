<?php
/**
 * Author: oke.ugwu
 * Date: 16/07/13 16:41
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Views\MapperList;
use Sidekick\Components\Phuse\Mappers\Package;

class PackagesList extends MapperList
{
  protected $_packages;
  protected $_heading;
  protected $_showFullList;
  private $_renderFilters = false;

  public function __construct($packages, $heading, $showFullList = true)
  {
    $this->_packages     = $packages;
    $this->_heading      = $heading;
    $this->_showFullList = $showFullList;
  }

  public function getPackages()
  {
    return $this->_packages;
  }

  public function getHeading()
  {
    return $this->_heading;
  }

  public function showFullList()
  {
    return $this->_showFullList;
  }

  public function renderFilters()
  {
    if($this->_renderFilters)
    {
      $vendor = Package::collection()->getUniqueField('vendor');
      return $this->filters($vendor);
    }

    return '';
  }

  public function showFilters()
  {
    $this->_renderFilters = true;
  }
}
