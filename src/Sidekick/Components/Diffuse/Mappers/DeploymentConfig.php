<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Helpers\DependencyArray;
use Cubex\Mapper\Database\RecordMapper;

/**
 * e.g. Live Stage, Live Prod, Dev Stage, Dev Prod
 */
class DeploymentConfig extends RecordMapper
{
  public $name;
  public $description;

  public function getTableName($plural = true)
  {
    return 'diffuse_platforms';
  }
}
