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

  public function getTableName($plural = true)
  {
    return 'diffuse_hosts_platforms';
  }

  /**
   * @return Host
   */
  public function host()
  {
    return $this->belongsTo(new Host());
  }

  /**
   * @return Platform
   */
  public function platform()
  {
    return $this->belongsTo(new Platform());
  }
}
