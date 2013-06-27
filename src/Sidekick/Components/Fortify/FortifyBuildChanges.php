<?php
/**
 * Author: oke.ugwu
 * Date: 27/06/13 09:50
 */

namespace Sidekick\Components\Fortify;

use Sidekick\Components\Fortify\Enums\BuildResult;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Repository\Mappers\Commit;

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
    $lastCommitHash = $lastCommitHash
                      ->setOrderBy("created_at", 'DESC')
                      ->setLimit(0, 1)
                      ->setColumns(['commit_hash'])
                      ->first();

    $findCommits = [$this->_commitHash];
    if($lastCommitHash !== null)
    {
      $findCommits[] = trim($lastCommitHash->commitHash);
    }

    $commitIds = Commit::collection()
                 ->whereIn('commit_hash', $findCommits)
                 ->setColumns(['commit_hash', 'repository_id', 'id'])
                 ->get();

    $range = Commit::collection();

    switch($commitIds->count())
    {
      case 2:
        $range->whereBetween("id", $commitIds->loadedIds());
        break;
      case 1:
        $range->whereLessThan("id", $commitIds->loadedIds()[0]);
        break;
    }

    $range->whereEq("repository_id", $commitIds->getField('repository_id'))
    ->setOrderBy('committed_at', 'DESC');

    return $range;
  }
}
