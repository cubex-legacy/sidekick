<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\ProjectAwareApplication;
use Sidekick\Applications\Diffuse\Controllers\DefaultController;
use Sidekick\Components\Users\Enums\UserRole;

class DiffuseApp extends ProjectAwareApplication
{
  public function getRoutes()
  {
    return [
      'manage-configs/(.*)'  => 'DeploymentConfigController',
      '/hosts/(.*)'          => 'HostController',
      '/deployments'         =>  'Projects\OverviewController@deployments',
      '/platforms/(.*)'      => 'PlatformController',
      '/configuration'       => [
        '/approval/(.*)' => 'Projects\Configuration\ApprovalController',
        '/platform/(.*)' => 'Projects\Configuration\PlatformConfigController',
        '/deployment'    => [
          '/stages/(.*)' => 'Projects\Configuration\DeploymentController',
          '/hosts/(.*)'  => 'Projects\Configuration\DeploymentHostsController',
          '(.*)'         => 'Projects\Configuration\DeploymentController',
        ],
        ''               => 'Projects\Configuration\DeploymentController',
      ],
      '/d/:deploymentId@num' => 'Projects\Deployments\DeploymentController',
      '/v/:versionId@num'    => [
        '/p/:platformId@num/(.*)' => 'Projects\Versions\VersionPlatformController',
        '/(.*)'                   => 'Projects\Versions\VersionDetailController',
      ],
      '/'                    => 'Projects\OverviewController',
      '/:buildId@num'        => 'Projects\OverviewController',
    ];
  }

  public function defaultController()
  {
    return null; //return new DefaultController();
  }

  public function getNavGroup()
  {
    return "Menu";
  }

  public function getBundles()
  {
    return [
      //new DebuggerBundle()
    ];
  }

  public function name()
  {
    return "Diffuse";
  }

  public function description()
  {
    return "Code Distribution";
  }

  public function userPermitted($userRole)
  {
    if($userRole == UserRole::USER)
    {
      return false;
    }
    return true;
  }
}
