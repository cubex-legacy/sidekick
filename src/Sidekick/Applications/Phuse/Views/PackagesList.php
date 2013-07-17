<?php
/**
 * Author: oke.ugwu
 * Date: 16/07/13 16:41
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Views\MapperList;

class PackagesList extends MapperList
{
  public $packages;
  public $heading;
  public $showFullList;

  public function __construct($packages, $heading, $showFullList = true)
  {
    $this->packages     = $packages;
    $this->heading      = $heading;
    $this->showFullList = $showFullList;
  }

  public function renderFilters()
  {
    $vendor  = $this->packages->getUniqueField('vendor');
    return $this->filters($vendor);
  }
}
