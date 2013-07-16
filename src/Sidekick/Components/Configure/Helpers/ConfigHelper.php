<?php
/**
 * Author: oke.ugwu
 * Date: 16/07/13 12:10
 */

namespace Sidekick\Components\Configure\Helpers;

use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Configure\Mappers\CustomConfigurationItem;
use Sidekick\Components\Configure\Mappers\Environment;
use Sidekick\Components\Configure\Mappers\EnvironmentConfigurationItem;
use Sidekick\Components\Phuse\Mappers\Package;
use Sidekick\Components\Projects\Mappers\Project;

class ConfigHelper
{
  private $_processed = [];

  public function getConfigArray($projectId, $environments)
  {
    $project     = new Project($projectId);
    $cascade     = $this->_cascade($project);
    $configArray = [];
    foreach($cascade as $level)
    {
      foreach($environments as $env)
      {
        $projectConfigs = EnvironmentConfigurationItem::collection(
          [
          'project_id'     => $level,
          'environment_id' => $env->id,
          ]
        );
        $configArray    = $this->_buildConfigArray(
          $projectConfigs,
          $configArray
        );
      }
    }

    return $configArray;
  }

  /**
   * @param $project \Sidekick\Components\Projects\Mappers\Project
   *
   * @return Array
   */
  private function _cascade($project)
  {
    if($project->parentId !== null)
    {
      $cascade[] = $project->parentId;
    }
    $cascade[] = $project->id();
    $cascade   = $this->_getCascadeFromDependencies(
      $project->id(),
      $cascade
    );

    return $cascade;
  }

  private function _getCascadeFromDependencies($projectId, $cascade)
  {
    //keep track of projects that we have processed, to help us avoid infinite
    //loops in cases where A depends on B and B depends on A
    $this->_processed[] = $projectId;

    //get packages project depends on
    $projectPackage = Package::collection(['project_id' => $projectId])
                      ->setOrderBy('id', 'DESC')->first();
    foreach($projectPackage->require as $required => $version)
    {
      //if required package is a project in sidekick, add it to the cascade
      $package = Package::collection(['name' => $required])
                 ->setOrderBy('id', 'DESC')->first();

      if($package && !in_array($package->projectId, $this->_processed))
      {
        if((new Project($package->projectId))->parentId != null)
        {
          $cascade[] = (new Project($package->projectId))->parentId;
        }
        $cascade[] = $package->projectId;

        $cascade = $this->_getCascadeFromDependencies(
          $package->projectId,
          $cascade
        );
      }
    }
    return $cascade;
  }

  private function _buildConfigArray($projectConfigs, $configArray)
  {
    $result = array();

    foreach($projectConfigs as $config)
    {
      $item  = new ConfigurationItem($config->configurationItemId);
      $group = new ConfigurationGroup($item->configurationGroupId);
      $env   = new Environment($config->environmentId);

      //check if custom value assigned
      if($config->customItemId !== null)
      {
        //override value before displaying
        $customItem  = new CustomConfigurationItem($config->customItemId);
        $item->value = $customItem->value;
      }

      $result[$env->name][$group->entry][$item->key] = is_object(
        $item->value
      ) ? (array)$item->value : $item->value;
    }

    $result = array_replace_recursive($result, $configArray);
    return $result;
  }
}
