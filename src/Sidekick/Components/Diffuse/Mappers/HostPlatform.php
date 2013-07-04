<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\PivotMapper;

class HostPlatform extends PivotMapper
{
  protected function _configure()
  {
    $this->pivotOn(new Host(), new Platform());
  }

  public function getTableName()
  {
    return 'diffuse_hosts_platforms';
  }
}
