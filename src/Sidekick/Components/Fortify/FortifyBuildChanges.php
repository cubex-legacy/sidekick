<?php
/**
 * Author: oke.ugwu
 * Date: 27/06/13 09:50
 */

namespace Sidekick\Components\Fortify;

use Sidekick\Components\Fortify\Enums\BuildResult;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\Source;

class FortifyBuildChanges
{
  public $projectId;
  public $buildId;
  public $runId;
  protected $_commitHash;

  public function __construct($projectId, $buildId, $commitHash, $runId = null)
  {
    $this->projectId   = $projectId;
    $this->buildId     = $buildId;
    $this->runId       = $runId;
    $this->_commitHash = $commitHash;
  }

  /**
   * @return \Cubex\Mapper\Database\RecordCollection
   */
  public function buildCommitRange()
  {
    $lastCommitHash = BuildRun::collection(
      [
      'project_id' => $this->projectId,
      'build_id'   => $this->buildId,
      'result'     => BuildResult::PASS
      ]
    );
    if($this->runId !== null)
    {
      $lastCommitHash->whereLessThan("id", $this->runId);
    }

    $range = array();
    $repo = Source::loadWhere(["project_id" => $this->projectId]);
    if($repo)
    {
      $branch   = Branch::loadWhere(["repository_id" => $repo->id()]);
      $branchId = $branch->id();

      $lastCommitHash = $lastCommitHash
        ->setOrderBy("created_at", 'DESC')
        ->setColumns(['commit_hash'])
        ->setLimit(0, 1)
        ->first();

      $findCommits = [
        $this->_commitHash,
        idp($lastCommitHash, "commitHash")
      ];

      $commitIds = Commit::collection()
        ->whereIn('commit_hash', $findCommits)
        ->whereEq('branch_id', $branchId)
        ->setColumns(['commit_hash', 'branch_id', 'id'])
        ->get();

      $range = Commit::collection();
      if(count($commitIds->loadedIds()) == 2)
      {
        $range->whereBetween("id", $commitIds->loadedIds());
      }
      $range->whereEq("branch_id", $branchId)
        ->setOrderBy('committed_at', 'DESC');
    }

    return $range;
  }
}
