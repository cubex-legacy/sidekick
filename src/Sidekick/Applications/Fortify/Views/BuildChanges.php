<?php
/**
 * Author: oke.ugwu
 * Date: 27/06/13 16:37
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Repository\Mappers\Source;

class BuildChanges extends TemplatedViewModel
{
  public $runId;
  protected $_repo;
  protected $_commits;

  public function __construct(Source $repo, $runId, $commits)
  {
    $this->_repo    = $repo;
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

  public function getCommitUrl($commit)
  {
    $diffusionBase = $this->getRepo()->diffusionBaseUri;
    if(!empty($diffusionBase))
    {
      return '"' . $diffusionBase . $commit->commitHash . '"';
    }
    else
    {
      return "/repository/commits/" . $commit->repository_id .
      "/" . $this->runId . "/" . $commit->id();
    }
  }
}
