<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Commit extends RecordMapper
{
  public $repositoryId;
  public $commitHash;
  public $message;
  public $author;
  public $commitedAt;
}
