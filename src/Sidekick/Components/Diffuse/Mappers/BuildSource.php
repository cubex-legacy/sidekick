<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class BuildSource extends RecordMapper
{
  /**
   * @enumclass \Sidekick\Components\Diffuse\Enum\RepositoryProvider
   */
  public $repositoryType;
  public $fetchUrl;
  public $branch = 'master';
  public $username;
  public $password;
}
