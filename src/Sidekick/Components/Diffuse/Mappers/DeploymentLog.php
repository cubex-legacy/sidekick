<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Servers\Mappers\Server;

class DeploymentLog extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;

  public $deploymentId;
  public $deploymentStageId;
  public $serverId;

  /**
   * @datatype tinyint
   */
  public $passed;
  public $command;
  public $log;
  public $stdOut;
  public $stdErr;
  /**
   * @datatype float
   */
  public $executionTime; //microtime(true)

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["deploymentId", "deploymentStageId", "serverId"]
    );
  }

  public function getTableName($plural = true)
  {
    return 'diffuse_deployments_stages_hosts';
  }

  /**
   * @return DeploymentStep
   */
  public function deploymentStage()
  {
    return $this->belongsTo(new DeploymentStep());
  }

  /**
   * @return Server
   */
  public function server()
  {
    return $this->belongsTo(new Server());
  }
}
