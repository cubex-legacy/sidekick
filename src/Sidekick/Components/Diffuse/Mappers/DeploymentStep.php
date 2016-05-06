<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Projects\Mappers\Project;

class DeploymentStep extends RecordMapper
{
  public $platformId;
  public $name;
  public $command;

  /**
   * @datatype int
   */
  public $order;

  /**
   * @return DeploymentConfig
   */
  public function platform()
  {
    return $this->belongsTo(new DeploymentConfig());
  }

  public function getTableName($plural = true)
  {
    return "diffuse_deployment_stages";
  }

}
