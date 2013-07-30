<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\PivotMapper;
use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Projects\Mappers\Project;

class HostPlatform extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;
  public $hostId;
  public $platformId;
  public $projectId;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["platform_id", "project_id", "host_id"]
    );
  }

  public function getTableName($plural = true)
  {
    return 'diffuse_hosts_platforms';
  }
}
