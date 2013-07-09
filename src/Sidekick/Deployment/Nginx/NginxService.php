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
        "Updating nginx on " . $host->name . " with new cfgs"
      );

      $stageHost->passed = true;
    }
    //Connect to server
    //Verify web root directories exist
    //Upload new configuration (provide config options or file ? )
    //Update symlinks
    //Restart service
  }

  public function buildConfig()
  {
    /*

  set $defaultVersion 'live';
  set $cubexVersion $defaultVersion;
  set $allowCookie true;
  if ( $http_cookie ~* "CUBEX_VERSION=" ) { set $cubexVersion $cookie_CUBEX_VERSION; }

  # Version should not contain double dots
  if ( $cubexVersion ~* "[\.]{2,}" ) { set $allowCookie false; }

  # Version should not contain a slash
  if ( $cubexVersion ~* "[/\\\\]" ) { set $allowCookie false; }

  # Allow version switching from approved IPs only
  if ( $remote_addr !~* "(192.168.0.21|192.168.0.20)" ) { set $allowCookie false; }

  # Only allow major version switching (1.X)
  if ( $cubexVersion !~* "(stage|1\.[0-9])" ) { set $allowCookie false; }

  # Check the directory exists
  if ( !-d  /home/cubex.nginx/$cubexVersion/public ) { set $allowCookie false; }

  # Revert back to live on any failed checks
  if ( $allowCookie = false ) { set $cubexVersion $defaultVersion; }

  root /home/cubex.nginx/$cubexVersion/public;

     */
  }
}
