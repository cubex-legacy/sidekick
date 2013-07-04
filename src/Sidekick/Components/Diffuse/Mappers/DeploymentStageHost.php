<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class DeploymentStageHost extends RecordMapper
{
  public $deploymentStageId;
  public $hostId;

  public $passed;
  public $log;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["deploymentStageId", "hostId"]
    );
  }

  public function getTableName()
  {
    return 'diffuse_deployments_stages_hosts';
  }

  /**
   * @return DeploymentStage
   */
  public function deploymentStage()
  {
    return $this->belongsTo(new DeploymentStage());
  }

  /**
   * @return Host
   */
  public function host()
  {
    return $this->belongsTo(new Host());
  }
}
