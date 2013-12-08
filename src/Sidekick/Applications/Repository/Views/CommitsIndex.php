<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 31/05/13
 * Time: 09:36
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Repository\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Repository\Helpers\DiffusionHelper;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\Source;

class CommitsIndex extends TemplatedViewModel
{
  protected $_branch;
  protected $_commits;

  public function __construct(Branch $branch, $commits)
  {
    $this->_branch  = $branch;
    $this->_commits = $commits;
  }

  /**
   * @return Commit[]
   */
  public function getCommits()
  {
    return $this->_commits;
  }

  /**
   * @return Source
   */
  public function getBranch()
  {
    return $this->_branch;
  }
}
