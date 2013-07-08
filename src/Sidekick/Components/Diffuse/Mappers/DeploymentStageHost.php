<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class DeploymentStageHost extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;

  public $deploymentId;
  public $deploymentStageId;
  public $hostId;

  /**
   * @datatype tinyint
   */
  public $passed;
  public $log;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["deploymentId", "deploymentStageId", "hostId"]
    );
  }

  public function getTableName($plural = true)
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
