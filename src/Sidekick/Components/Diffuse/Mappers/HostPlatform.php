<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class HostPlatform extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;
  protected $_autoTimestamp = false;

  public $hostId;
  public $platformId;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["hostId", "platformId"]
    );
  }

  public function getTableName()
  {
    return 'diffuse_hosts_platforms';
  }

  public function platform()
  {
    return new Platform($this->platformId);
  }
}
