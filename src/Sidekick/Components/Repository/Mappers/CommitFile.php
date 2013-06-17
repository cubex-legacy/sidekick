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
  /**
   * @length 255
   */
  public $filePath;
  public $changeType;
}
