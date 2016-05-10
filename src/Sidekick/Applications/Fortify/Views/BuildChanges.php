<?php
/**
 * Author: oke.ugwu
 * Date: 27/06/13 16:37
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\Source;

class BuildChanges extends TemplatedViewModel
{
  public $runId;
  protected $_repo;
  protected $_commits;

  public function __construct($runId, $commits)
  {
    $this->runId    = $runId;
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
  public function getRepo()
  {
    return $this->_repo;
  }
}
