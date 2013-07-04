<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class DeploymentStage extends RecordMapper
{
  public $deploymentId;
  public $serviceClass;
  public $requireAllHostsPass;
  public $configuration = [];

  protected function _configure()
  {
    $this->_setSerializer("configuration");
  }

  /**
   * @return Deployment
   */
  public function deployment()
  {
    return $this->belongsTo(new Deployment());
  }
}
