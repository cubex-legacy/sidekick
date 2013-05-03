<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Controllers;

use Cubex\Form\Form;
use Cubex\Facade\Redirect;
use Cubex\Mapper\Database\RecordCollection;
use Sidekick\Applications\Configurator\Views\ConfigGroupView;
use Sidekick\Applications\Configurator\Views\ConfigItemsManager;
use Sidekick\Applications\Configurator\Views\EnvironmentList;
use Sidekick\Applications\Configurator\Views\ModifyProjectConfigItem;
use Sidekick\Applications\Configurator\Views\ProjectConfigurator;
use Sidekick\Applications\Configurator\Views\ProjectList;
use Sidekick\Components\Configure\ConfigWriter;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Configure\Mappers\CustomConfigurationItem;
use Sidekick\Components\Configure\Mappers\Environment;
use Sidekick\Components\Configure\Mappers\EnvironmentConfigurationItem;
use Sidekick\Components\Projects\Mappers\Project;

class DefaultController extends ConfiguratorController
{
  public function __construct()
  {
    $this->setBaseUri('/configurator');
  }

  public function renderIndex()
  {
    $projectId         = $this->getInt('projectId');
    $projectCollection = new RecordCollection(new Project());

    if($projectId === null)
    {
      $projects = $projectCollection->loadWhere("%C IS NULL", "parent_id");
    }
    else
    {
      $projects = $projectCollection->loadWhere(["parent_id" => $projectId]);
    }

    $subProjects = Project::conn()->getKeyedRows(
      "SELECT id, (
        SELECT count(*) FROM " . Project::tableName() . " WHERE parent_id= p.id
        ) as sub_projects
        FROM " . Project::tableName() . " p
      "
    );

    $configGroups = ConfigurationGroup::conn()->getKeyedRows(
      "SELECT project_id, count(*)
       FROM " . ConfigurationGroup::tableName() . " GROUP BY project_id
      "
    );

    $pl = new ProjectList();
    $pl->setProjects($projects)
    ->setSubProjects($subProjects)
    ->setConfigGroups($configGroups);

    return $pl;
  }

  public function renderEnvironments()
  {
    $envs = Environment::collection()->loadAll();
    return new EnvironmentList($envs);
  }

  public function renderConfigGroups()
  {
    $projectId = $this->getInt('projectId');
    echo "<h1>Config Groups</h1>";
    echo "<span><a href='/configurator/add-config-group/$projectId'>Add New Group</a>";
    $configGroups = ConfigurationGroup::collection()->loadWhere(
      [
      'project_id' => $projectId
      ]
    );
    foreach($configGroups as $group)
    {
      echo '<li><a href="/configurator/config-items/' .
      $group->id . '">' . $group->groupName . '</a></li>';
    }
  }

  public function renderProjectConfigs()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId', 1);

    return new ProjectConfigurator($projectId, $envId);
  }

  public function addProjectConfigItem()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId');
    $itemId    = $this->getInt('itemId');

    $ec                      = new EnvironmentConfigurationItem();
    $ec->environmentId       = $envId;
    $ec->projectId           = $projectId;
    $ec->configurationItemId = $itemId;
    $ec->saveChanges();

    $url = '/configurator/project-configs/' . $projectId;
    if($envId)
    {
      $url = '/configurator/project-configs/' . $projectId . '/' . $envId;
    }
    Redirect::to($url)->now();
  }

  public function removeProjectConfigItem()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId');
    $itemId    = $this->getInt('itemId');

    //remove from environment specific items
    $eci          = new EnvironmentConfigurationItem(
      [
      $projectId,
      $envId,
      $itemId
      ]
    );
    $customItemId = $eci->customItemId;
    $eci->delete();

    if($customItemId !== null)
    {
      $itemInUse = EnvironmentConfigurationItem::collection()
      ->whereEq('custom_item_id', $customItemId);

      if($itemInUse->count() == 0)
      {
        $temp = new CustomConfigurationItem($customItemId);
        $temp->delete();
      }
    }

    $url = '/configurator/project-configs/' . $projectId;
    if($envId)
    {
      $url = '/configurator/project-configs/' . $projectId . '/' . $envId;
    }
    Redirect::to($url)->now();
  }

  public function renderModifyProjectConfigItem()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId');
    $itemId    = $this->getInt('itemId');

    return new ModifyProjectConfigItem($projectId, $envId, $itemId);
  }

  public function postModifyProjectConfigItem()
  {
    $postData = $this->request()->postVariables();
    if(isset($postData['key']))
    {
      $item        = new ConfigurationItem($postData['itemId']);
      $item->value = $item->prepValueIn($postData['value'], $item->type);

      if($item->getAttribute('value')->isModified())
      {
        //key was modified. If new value matches an existing customItem,
        //then don't create a new customItem, just reuse its ID
        //on new EnvironmentConfiguration

        $customItem = CustomConfigurationItem::collection()->loadOneWhere(
          [
          'item_id' => $postData['itemId'],
          'value'   => $item->value
          ]
        );

        if($customItem === null)
        {
          //create new custom item
          $customItem         = new CustomConfigurationItem();
          $customItem->itemId = $postData['itemId'];
          $customItem->value  = $item->value;
          $customItem->saveChanges();
        }

        //relate new item to project & environment
        $pe                      = new EnvironmentConfigurationItem();
        $pe->projectId           = $postData['projectId'];
        $pe->environmentId       = $postData['envId'];
        $pe->configurationItemId = $postData['itemId'];
        $pe->customItemId        = $customItem->id();
        $pe->saveChanges();

        $url = '/configurator/project-configs/' . $postData['projectId'];
        if($postData['envId'])
        {
          $url = '/configurator/project-configs/' .
          $postData['projectId'] . '/' . $postData['envId'];
        }
        Redirect::to($url)->now();
      }
    }
  }

  private function _arrayMergeIni(array &$arrayOne, array &$arrayTwo)
  {
    $merged = $arrayOne;
    foreach($arrayTwo as $key => &$value)
    {
      if(is_array($value) && isset ($merged[$key]) && is_array($merged[$key]))
      {
        $merged[$key] = $this->_arrayMergeIni($merged[$key], $value);
      }
      else
      {
        $merged[$key] = $value;
      }
    }
    return $merged;
  }

  public function buildIni()
  {
    $projectId = $this->getInt('projectId');
    $project   = new Project($projectId);

    echo "<h1>$project->name</h1>";

    //load config in cascade fashion, parent comes first
    if($project->parentId !== null)
    {
      $cascade[] = $project->parentId;
    }
    $cascade[] = $projectId;

    $envs        = Environment::collection()->loadAll();
    $configArray = [];
    foreach($cascade as $level)
    {
      foreach($envs as $env)
      {
        $projectConfigs = EnvironmentConfigurationItem::collection()
        ->loadWhere(
          [
          'project_id'     => $level,
          'environment_id' => $env->id,
          ]
        );

        $configArray = $this->_arrayMergeIni(
          $configArray, $this->buildCascadeConfig($projectConfigs)
        );
      }
    }

    ksort($configArray);

    foreach($envs as $env)
    {
      echo "<h3>$env->filename</h3>";
      $cw = new ConfigWriter();
      echo "<pre>";
      $cw->buildIni($configArray[$env->name], true);
      echo "</pre>";
    }
  }

  public function buildCascadeConfig($projectConfigs)
  {
    $configArray = array();

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

      $configArray[$env->name][$group->entry][$item->key] = is_object(
        $item->value
      ) ? (array)$item->value : $item->value;
    }

    return $configArray;
  }


  public function renderAddConfigGroup()
  {
    return new ConfigGroupView($this->getInt('projectId'));
  }

  public function renderConfigItems()
  {
    $groupId = $this->getInt("groupId");
    return new ConfigItemsManager($groupId);
  }

  public function postAddingConfigGroup()
  {
    $postData    = $this->request()->postVariables();
    $configGroup = new ConfigurationGroup();
    $configGroup->hydrate($postData);
    $configGroup->saveChanges();

    var_dump($configGroup);
    Redirect::to('/configurator')->now();
  }

  public function postAddingConfigItem()
  {
    $postData = $this->request()->postVariables();
    if(isset($postData['kv']))
    {
      foreach($postData['kv'] as $itemId => $data)
      {
        if($data['key'] != '' && $data['value'] != '')
        {
          if($itemId != '*')
          {
            $item                       = new ConfigurationItem($itemId);
            $item->key                  = $data['key'];
            $item->value                = $item->prepValueIn(
              $data['value'], $data['type']
            );
            $item->type                 = $data['type'];
            $item->configurationGroupId = $postData['groupId'];
            if($item->isModified())
            {
              //update existing item
              $item->saveChanges();
            }
          }
          else
          {
            //new item
            $item                       = new ConfigurationItem();
            $item->key                  = $data['key'];
            $item->value                = $item->prepValueIn(
              $data['value'], $data['type']
            );
            $item->type                 = $data['type'];
            $item->configurationGroupId = $postData['groupId'];
            $item->saveChanges();
          }
        }
      }
    }

    Redirect::to(
      '/configurator/config-items/' . $postData['groupId']
    )->now();
  }


  public function getRoutes()
  {
    return array(
      '/'                                                     => 'index',
      '/project/:projectId'                                   => 'index',
      '/project-configs/:projectId'                           => 'projectConfigs',
      '/config-groups/:projectId'                             => 'configGroups',
      '/build-ini/:projectId/'                                => 'buildIni',
      '/project-configs/:projectId/:envId'                    => 'projectConfigs',
      '/add-project-config-item/:projectId/:envId/:itemId'    => 'addProjectConfigItem',
      '/remove-project-config-item/:projectId/:envId/:itemId' => 'removeProjectConfigItem',
      '/modify-project-config-item/:projectId/:envId/:itemId' => 'modifyProjectConfigItem',
      '/add-config-group/:projectId'                          => 'addConfigGroup',
      '/config-items/:groupId'                                => 'configItems',
      '/adding-config-group'                                  => 'addingConfigGroup',
      '/adding-config-item'                                   => 'addingConfigItem',
      '/modify-project-config-item'                           => 'modifyProjectConfigItem',
      '/environments'                                         => 'environments',
    );
  }
}