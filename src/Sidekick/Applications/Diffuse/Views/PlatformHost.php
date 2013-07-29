<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 29/07/13
 * Time: 11:29
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\Host;
use Sidekick\Components\Diffuse\Mappers\HostPlatform;
use Sidekick\Components\Diffuse\Mappers\Platform;

class PlatformHost extends TemplatedViewModel
{

  protected $_projectId;

  public function __construct($projectId)
  {
    $this->_projectId = $projectId;
  }

  public function getHostPlatforms()
  {
    return HostPlatform::collection()->loadWhere(
      ["project_id" => $this->_projectId]
    );
  }

  public function getPlatformName($id)
  {
    $platform = Platform::collection()->loadOneWhere(["id" => $id]);
    return ($platform == null) ? "" : $platform->name;
  }

  public function getHostName($id)
  {
    $host = Host::collection()->loadOneWhere(["id" => $id]);
    return ($host == null) ? "" : $host->name;
  }
}
