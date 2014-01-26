<?php
namespace Sidekick\Applications\Overview\Views;

use Cubex\Text\TextTable;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\CommitBuild;
use Sidekick\Components\Fortify\Mappers\CommitBuildInsight;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Repository;

class ProjectOverview extends TemplatedViewModel
{
  protected $_project;
  protected $_repository;
  protected $_branch;
  protected $_commitBuild;
  protected $_commitBuilds;
  /**
   * @var CommitBuildInsight
   */
  protected $_insight;

  public function __construct(Project $project)
  {
    $this->_project = $project;
  }

  public function getProject()
  {
    return $this->_project;
  }

  public function setRepository(Repository $repository)
  {
    $this->_repository = $repository;
    return $this;
  }

  public function setBranch(Branch $branch)
  {
    $this->_branch = $branch;
    return $this;
  }

  public function setCommitBuilds($commitBuilds)
  {
    $this->_commitBuild  = $commitBuilds->first();
    $this->_commitBuilds = $commitBuilds;
    return $this;
  }

  public function setInsight(CommitBuildInsight $insight)
  {
    $this->_insight = $insight;
    return $this;
  }

  public function getBuildProcessTable()
  {
    $results = [];
    if($this->_insight)
    {
      foreach($this->_insight as $k => $v)
      {
        if(starts_with($k, 'process:'))
        {
          list(, $stage, $alias) = explode(":", $k);
          $results[$stage][$alias] = $v;
        }
      }
    }

    return $results;
  }
}
