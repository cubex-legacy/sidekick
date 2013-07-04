<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class DeploymentStage extends RecordMapper
{
  public $platformId;
  public $serviceClass;
  public $requireAllHostsPass;
  public $configuration = [];
  public $dependencies = [];

  protected function _configure()
  {
    $this->_setSerializer("configuration");
    $this->_setSerializer("dependencies");
  }

  /**
   * @return Platform
   */
  public function platform()
  {
    return $this->belongsTo(new Platform());
  }
}
