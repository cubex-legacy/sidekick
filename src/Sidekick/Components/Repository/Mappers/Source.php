<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Repository\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Enums\RepositoryProvider;

class Source extends RecordMapper
{
  /**
   * @enumclass \Sidekick\Components\Repository\Enum\RepositoryProvider
   */
  public $repositoryType;
  public $diffusionBaseUri;
  public $name;
  /**
   * Phabricator Uri e.g. http://phabricator.cubex.io/r{CALLSIGN}
   */
  public $description;
  public $localpath;
  public $fetchUrl;
  public $branch = 'master';
  public $username;
  public $password;
  public $projectId;
  public $commitBuildId;

  protected function _configure()
  {
    $this->_setRequired('repositoryType');
  }

  public function project()
  {
    return $this->belongsTo(new Project());
  }

  public function repositoryTypes()
  {
    return new RepositoryProvider();
  }

  public function commits()
  {
    return $this->hasMany(new Commit(), 'repository_id');
  }

  public function commitBuild()
  {
    return $this->belongsTo(new Build(), 'commit_build_id');
  }
}
