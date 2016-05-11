<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 07/06/13
 * Time: 16:43
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\Helpers\DateTimeHelper;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Users\Mappers\User;

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

  /**
   * @param $build
   *
   * @return User
   */
  public function getBuildUser($build)
  {
    if(isset($build->user_id) && (int)$build->user_id > 0)
    {
      return (new User($build->user_id))->username;
    }

    return '--';
  }

  public function getDuration($endDate, $startDate)
  {
    $diff = strtotime($endDate) - strtotime($startDate);
    return DateTimeHelper::formatTimespan($diff);
  }

  public function textClass($result)
  {
    $return = "text-info";
    if($result == 'fail')
    {
      $return = "text-error";
    }
    elseif($result == 'pass')
    {
      $return = "text-success";
    }
    return $return;
  }
}
