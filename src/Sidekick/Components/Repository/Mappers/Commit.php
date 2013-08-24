<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Repository\Mappers;

use Cubex\Mapper\Database\RecordMapper;

/**
 * Class Commit
 * @unique repository_id,commit_hash
 * @index  repository_id
 */
class Commit extends RecordMapper
{
  public $repositoryId;
  public $commitHash;
  public $subject;
  /**
   * @datatype text
   */
  public $message;
  public $author;
  public $committedAt;

  /**
   * @param      $commitOne
   * @param null $commitTwo
   *
   * @return \Cubex\Mapper\Database\RecordCollection|Commit[]
   */
  public static function collectionBetween($commitOne, $commitTwo = null)
  {
    $findCommits = [$commitOne];
    if($commitTwo !== null)
    {
      $findCommits[] = $commitTwo;
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
