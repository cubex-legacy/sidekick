<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class HostPlatform extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;
  public $serverId;
  public $platformId;
  public $projectId;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["platform_id", "project_id", "server_id"]
    );
  }

  public function getTableName($plural = true)
  {
    return 'diffuse_hosts_platforms';
  }
}
