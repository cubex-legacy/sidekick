<?php
/**
 * Author: oke.ugwu
 * Date: 27/08/13 17:51
 */

namespace Sidekick\Components\Diffuse\Helpers;

use Cubex\Foundation\Container;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;

class DeploymentHelper
{
  public static function getServiceClassOptions()
  {
    $serviceClassOptions = [];
    $projectBase         = Container::config()->get('_cubex_')
                           ->getStr('project_base');
    $deploymentDir       = $projectBase . "Sidekick/Deployment/";

    if(file_exists($deploymentDir))
    {
      $files = scandir($deploymentDir);
      foreach($files as $file)
      {
        if($file != '.' && $file != '..')
        {
          if(is_dir($deploymentDir . $file))
          {
            $classes = glob($deploymentDir . $file . '/*.php');
            foreach($classes as $class)
            {
              $pathInfo  = pathinfo($class);
              $className = $pathInfo['filename'];
              $fullName  = "\\Sidekick\\Deployment\\$file\\$className";

              $serviceClassOptions[$fullName] = $file;
            }
          }
        }
      }
    }

    if(empty($serviceClassOptions))
    {
      throw new \Exception('No Deployment Service Classes found in ' . $deploymentDir);
    }

    return $serviceClassOptions;
  }

  public static function getRelevantServiceClassOptions($projectId, $platformId)
  {
    $availableServiceClasses = DeploymentStage::collection()
                               ->load(
                                   [
                                   'project_id'  => $projectId,
                                   'platform_id' => $platformId
                                   ]
                                 )->getUniqueField('serviceClass');

    $serviceClassOptions = self::getServiceClassOptions();
    foreach($serviceClassOptions as $fullName => $file)
    {
      if(in_array($fullName, $availableServiceClasses))
      {
        unset($serviceClassOptions[$fullName]);
      }
    }

    return $serviceClassOptions;
  }
}
