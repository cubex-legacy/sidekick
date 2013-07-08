<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment\Nginx;

use Cubex\Log\Log;
use Sidekick\Deployment\BaseDeploymentService;

class NginxService extends BaseDeploymentService
{
  /**
   * @return bool success
   */
  public function deploy()
  {
    foreach($this->_hosts as $stageHost)
    {
      $host = $stageHost->host();

      Log::info(
        "Updating nginx on " . $host->hostname . " with new cfgs"
      );

      $stageHost->passed = true;
    }
    //Connect to server
    //Verify web root directories exist
    //Upload new configuration (provide config options or file ? )
    //Restart service
  }
}
