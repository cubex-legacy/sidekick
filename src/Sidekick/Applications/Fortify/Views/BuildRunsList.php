<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 07/06/13
 * Time: 16:43
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;

class BuildRunsList extends TemplatedViewModel
{
  protected $_buildRuns;

  public function __construct($buildRuns)
  {
    $this->_buildRuns = $buildRuns;
  }

  public function getBuildRuns()
  {
    return $this->_buildRuns;
  }

  public function getDuration($endDate, $startDate)
  {
    $duration = new \stdClass();
    $duration->days = 0;
    $duration->hours = 0;
    $duration->mins = 0;
    $duration->seconds = 0;

    $diff = strtotime($endDate) - strtotime($startDate);
    $rem = $diff;

    $duration->days = floor($diff / 86400);
    $rem = $rem - ($duration->days * 86400);

    $duration->hours = floor ($rem / 3600);
    $rem = $rem - ($duration->hours * 3600);

    $duration->mins = floor ($rem / 60);
    $rem = $rem -($duration->mins * 60);

    $duration->secs = $rem;

    $display = [];
    foreach($duration as $k => $v)
    {
      if($v > 0)
      {
        $display[] = $v.$k;
      }
    }

    return implode(' ', $display);
  }
}
