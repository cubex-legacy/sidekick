<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Repository\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Source extends RecordMapper
{
  /**
   * @enumclass \Sidekick\Components\Repository\Enum\RepositoryProvider
   */
  public $repositoryType;
  public $name;
  public $description;
  public $localpath;
  public $fetchUrl;
  public $branch = 'master';
  public $username;
  public $password;
}
