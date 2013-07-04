<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\PivotMapper;

class DeploymentHost extends PivotMapper
{
  public $passed;

  protected function _configure()
  {
    $this->pivotOn(new Deployment(), new Host());
  }

  public function getTableName()
  {
    return 'diffuse_deployments_hosts';
  }
}
