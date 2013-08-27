<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse;

use Bundl\Debugger\DebuggerBundle;
use Sidekick\Applications\BaseApp\BaseApp;
use Sidekick\Applications\Diffuse\Controllers\DefaultController;

class DiffuseApp extends BaseApp
{
  public function getRoutes()
  {
    return [
      '/'                        => 'DefaultController',
      '/hosts/(.*)'              => 'HostController',
      '/platforms/(.*)'          => 'PlatformController',
      '/projects/:projectId@num' => [
        '/configuration'       => [
          '/approval/(.*)'   => 'Projects\Configuration\ApprovalController',
          '/deployment/(.*)' => 'Projects\Configuration\DeploymentController',
          ''                 => 'Projects\Configuration\DeploymentController',
        ],
        '/d/:deploymentId@num' => 'Projects\Deployments\DeploymentController',
        '/v/:versionId@num'    => [
          '/p/:platformId@num/(.*)' => 'Projects\Versions\VersionPlatformController',
          '/(.*)'                   => 'Projects\Versions\VersionDetailController',
        ],
        '/'                    => 'Projects\OverviewController',
      ],
      '/:projectId@num'          => [
        '/stages/(.*)' => 'Project\StagesController',
        '/hosts/(.*)'  => 'Project\HostsController',
      ]
    ];
  }

  public function defaultController()
  {
    return null; //return new DefaultController();
  }

  public function getNavGroup()
  {
    return "Deployment";
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
}
