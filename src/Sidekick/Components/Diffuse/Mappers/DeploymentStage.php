<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Projects\Mappers\Project;

class DeploymentStage extends RecordMapper
{
  public $projectId;
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

  /**
   * @return Project
   */
  public function project()
  {
    return $this->belongsTo(new Project());
  }
}
