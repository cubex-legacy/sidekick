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

  public function getPlatforms()
  {
    return Platform::collection()->loadAll();
  }

  public function getHostsForPlatform($platformId)
  {
    return HostPlatform::collection()->loadWhere(
      ["project_id" => $this->_projectId, "platform_id" => $platformId]
    );
  }

  public function getHostName($id)
  {
    $host = Host::collection()->loadOneWhere(["id" => $id]);
    return ($host == null) ? "" : $host->name;
  }
}
