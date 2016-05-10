<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class BuildChanges extends RecordMapper
{
  public $repositoryId;
  public $commitHash;
  public $subject;
  public $message;
  public $author;
  public $committedAt;
  public $buildRunId;

  public function getTableName($plural = true)
  {
    return 'fortify_build_changes';
  }
}
