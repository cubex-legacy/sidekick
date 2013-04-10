<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Repository\Mappers;

use Cubex\Mapper\Database\RecordMapper;

/**
 * Class Commit
 * @unique repository_id,commit_hash
 * @index repository_id
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
}
