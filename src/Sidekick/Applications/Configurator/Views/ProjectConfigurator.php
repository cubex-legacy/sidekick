<?php
/**
 * @author: oke.ugwu
 *        Application: Configurator/Views
 */
namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Configure\Mappers\CustomConfigurationItem;
use Sidekick\Components\Configure\Mappers\Environment;
use Sidekick\Components\Configure\Mappers\EnvironmentConfigurationItem;
use Sidekick\Components\Projects\Mappers\Project;

class ProjectConfigurator extends TemplatedViewModel
{
  public $project;
  public $parentProject;
  public $currentEnvironment;
  public $environments;
  public $availableConfigs;
  public $environmentConfig;

  public function __construct($projectId, $envId)
  {
    $this->project            = new Project($projectId);
    $this->currentEnvironment = new Environment($envId);
    $this->environments       = Environment::collection()->loadAll();
    $this->parentProject      = new Project($this->project->parentId);

    $this->_getAvailableConfigs();
    $this->_getEnvironmentConfig();
  }

  private function _getAvailableConfigs()
  {
    $in[] = $this->project->id();
    if($this->project->parentId !== null)
    {
      $in[] = $this->project->parentId;
    }

    $configGroups = ConfigurationGroup::collection()
      ->loadWhere("project_id IN (" . implode(',', $in) . ")")
      ->setOrderBy("group_name");

    $this->availableConfigs = [];
    foreach($configGroups as $group)
    {
      $configItems = ConfigurationItem::collection()
        ->loadWhere(['configuration_group_id' => $group->id]);

      $this->availableConfigs[$group->groupName] = $configItems;
    }
  }

  private function _getEnvironmentConfig()
  {
    $projectConfigs = EnvironmentConfigurationItem::collection()
      ->loadWhere(
        [
        'project_id'     => $this->project->id(),
        'environment_id' => $this->currentEnvironment->id(),
        ]
      );

    $this->environmentConfig = array();
    foreach($projectConfigs as $config)
    {
      $item  = new ConfigurationItem($config->configurationItemId);
      $group = new ConfigurationGroup($item->configurationGroupId);

      //check if custom value assigned
      if($config->customItemId !== null)
      {
        //override value before displaying
        $customItem  = new CustomConfigurationItem($config->customItemId);
        $item->value = $customItem->value;
      }
      $this->environmentConfig[$group->groupName][$item->key] = $item;
    }
    ksort($this->environmentConfig);
  }

  public function getBreadcrumbs()
  {
    $breadcrumbs = new Breadcrumbs();
    $breadcrumbs->addItem('All Projects', $this->baseUri());
    if($this->parentProject->exists())
    {
      $breadcrumbs->addItem(
        $this->parentProject->name,
        $this->baseUri() . '/project/' . $this->parentProject->id()
      );
    }
    $breadcrumbs->addItem(
      $this->project->name . ' <span class="muted">Configure</span>'
    );

    return $breadcrumbs;
  }
}