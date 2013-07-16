<?php
/**
 * Author: oke.ugwu
 * Date: 16/07/13 17:47
 */

namespace Sidekick\Applications\Phuse\Views;

use Cubex\View\TemplatedViewModel;

class RecentReleases extends TemplatedViewModel
{
  public $releases;

  public function __construct($releases)
  {
    $this->releases = $releases;
  }
}
