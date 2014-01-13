<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Repository\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Repository\Enums\ChangeType;

/**
 * Class Commit
 * @index  branch_id
 * @unique branch_id,commit_hash
 */
class Commit extends RecordMapper
{
  public $branchId;
  public $commitHash;
  public $subject;
  /**
   * @datatype text
   */
  public $message;
  public $author;
  public $committedAt;

  const INCLUDE_BOTH   = 'both';
  const INCLUDE_OLDEST = 'oldest';
  const INCLUDE_LATEST = 'latest';

  /**
   * @param        $commitOne
   * @param null   $commitTwo
   * @param string $inclusion
   *
   * @return \Cubex\Mapper\Database\RecordCollection|Commit[]
   */
  public static function collectionBetween(
    $commitOne, $commitTwo = null, $inclusion = self::INCLUDE_BOTH
  )
  {
    $findCommits = [$commitOne];
    if($commitTwo !== null)
    {
      $findCommits[] = $commitTwo;
    }

    $commitIds = Commit::collection()
      ->whereIn('commit_hash', $findCommits)
      ->setColumns(['commit_hash', 'branch_id', 'id'])
      ->get();

    $range = Commit::collection();

    switch($commitIds->count())
    {
      case 2:
        $ids = $commitIds->loadedIds();
        sort($ids);
        switch($inclusion)
        {
          case self::INCLUDE_BOTH:
            $range->whereBetween("id", $commitIds->loadedIds());
            break;
          case self::INCLUDE_LATEST:
            $range->loadWhereAppend("%C <= %s", "id", last($ids));
            $range->loadWhereAppend("%C > %s", "id", head($ids));
            break;
          case self::INCLUDE_OLDEST:
            $range->loadWhereAppend("%C < %s", "id", last($ids));
            $range->loadWhereAppend("%C >= %s", "id", head($ids));
            break;
        }
        break;
      case 1:
        if($inclusion === self::INCLUDE_LATEST)
        {
          $range->loadWhereAppend("%C <= %s", "id", $commitIds->loadedIds()[0]);
        }
        else
        {
          $range->whereLessThan("id", $commitIds->loadedIds()[0]);
        }
        break;
    }

    $range->whereEq("branch_id", $commitIds->getField('branch_id'))
      ->setOrderBy('committed_at', 'DESC');

    return $range;
  }

  public function commitFiles(array $changeTypes = null)
  {
    $relationship = $this->hasMany(new CommitFile());

    if($changeTypes !== null)
    {
      $relationship->whereIn("change_type", $changeTypes);
      return $relationship;
    }

    return $relationship;
  }
}
