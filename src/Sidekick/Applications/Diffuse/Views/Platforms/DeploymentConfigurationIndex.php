<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 10:29
 */

namespace Sidekick\Applications\Diffuse\Views\Platforms;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\Build;

class DeploymentConfigurationIndex extends TemplatedViewModel
{
  protected $_platforms;

  public function __construct($platforms)
  {
    $this->_platforms = $platforms;
  }

  public function getDeploymentConfigs()
  {
    return $this->_platforms;
  }

  public function getRequiredBuilds($buildIdArr)
  {
    return Build::collection()->loadIds($buildIdArr)->getUniqueField('name');
  }
}
