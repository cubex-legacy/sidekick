<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Repository\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Enums\RepositoryProvider;

class Repository extends RecordMapper
{
  public $projectId;
  /**
   * @enumclass \Sidekick\Components\Repository\Enum\RepositoryProvider
   */
  public $repositoryType;

  public $name;
  public $description;

  public $localpath;
  public $fetchUrl;

  public $username;
  public $password;

  public function repositoryTypes()
  {
    return new RepositoryProvider();
  }

  protected function _configure()
  {
    $this->_setRequired('repositoryType');
    $this->_setRequired('projectId');
  }

  public function project()
  {
    return $this->belongsTo(new Project());
  }

  public function branches()
  {
    return $this->hasMany(new Branch());
  }
}
