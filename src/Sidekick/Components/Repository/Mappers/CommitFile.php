<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Repository\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class CommitFile extends RecordMapper
{
  public $commitId;
  public $repositoryId;
  public $filePath;
  public $changeType;
}
