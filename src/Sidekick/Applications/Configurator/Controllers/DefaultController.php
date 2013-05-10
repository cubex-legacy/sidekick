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
use Sidekick\Applications\Configurator\Views\IniPreview;
use Sidekick\Applications\Configurator\Views\ModifyProjectConfigItem;
use Sidekick\Applications\Configurator\Views\ProjectConfigurator;
use Sidekick\Applications\Configurator\Views\ProjectList;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Configure\Mappers\CustomConfigurationItem;
use Sidekick\Components\Configure\Mappers\Environment;
use Sidekick\Components\Configure\Mappers\EnvironmentConfigurationItem;
use Sidekick\Components\Projects\Mappers\Project;

class DefaultController extends ConfiguratorController
{
  public function renderIndex()
  {
    $projectId         = $this->getInt('projectId');
    $projectCollection = new RecordCollection(new Project());
    $parentProject     = null;

    if($projectId === null)
    {
      $projects = $projectCollection->loadWhere("%C IS NULL", "parent_id");
    }
    else
    {
      $projects      = $projectCollection->loadWhere(
        ["parent_id" => $projectId]
      );
      $parentProject = new Project($projectId);
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

    $pl = $this->createView(new ProjectList());
    $pl->setProjects($projects)
      ->setSubProjects($subProjects)
      ->setConfigGroups($configGroups)
      ->setParentProject($parentProject);

    return $pl;
  }

  public function renderEnvironments()
  {
    $envs = Environment::collection()->loadAll();
    return $this->createView(new EnvironmentList($envs));
  }

  public function renderConfigGroups()
  {
    $projectId = $this->getInt('projectId');
    return $this->createView(new ConfigGroupView($projectId));
  }

  public function renderProjectConfigs()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId', 1);

    return $this->createView(new ProjectConfigurator($projectId, $envId));
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

    $url = $this->baseUri() . '/project-configs/' . $projectId;
    if($envId)
    {
      $url = $this->baseUri() . '/project-configs/' . $projectId . '/' . $envId;
    }
    Redirect::to($url)->now();
  }

  public function removeProjectConfigItem()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId');
    $itemId    = $this->getInt('itemId');

    //remove from environment specific items
    $this->_removeConfig($projectId, $envId, $itemId);

    $url = $this->baseUri() . '/project-configs/' . $projectId;
    if($envId)
    {
      $url = $this->baseUri() . '/project-configs/' . $projectId . '/' . $envId;
    }
    Redirect::to($url)->now();
  }

  public function renderModifyProjectConfigItem()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId');
    $itemId    = $this->getInt('itemId');

    return $this->createView(
      new ModifyProjectConfigItem($projectId, $envId, $itemId)
    );
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

        $customItem = CustomConfigurationItem::loadWhereOrNew(
          [
          'item_id' => $postData['itemId'],
          'value'   => json_encode($item->value)
          ]
        );
        /**
         * @var $customItem CustomConfigurationItem
         */
        if(!$customItem->exists())
        {
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

        $url = $this->baseUri() . '/project-configs/' . $postData['projectId'];
        if($postData['envId'])
        {
          $url = $this->baseUri() . '/project-configs/' .
            $postData['projectId'] . '/' . $postData['envId'];
        }
        Redirect::to($url)->now();
      }
      else
      {
        //config item is being reverted.
        //remove old one
        $this->_removeConfig(
          $postData['projectId'],
          $postData['envId'],
          $postData['itemId']
        );

        //create new one
        $pe                      = new EnvironmentConfigurationItem();
        $pe->projectId           = $postData['projectId'];
        $pe->environmentId       = $postData['envId'];
        $pe->configurationItemId = $postData['itemId'];
        $pe->saveChanges();

        $url = $this->baseUri() . '/modify-project-config-item/' .
          $postData['projectId'] . '/' .
          $postData['envId'] . '/' .
          $postData['itemId'];
        Redirect::to($url)->now();
      }
    }
  }

  public function buildIni()
  {
    $projectId = $this->getInt('projectId');
    $project   = new Project($projectId);

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

        $configArray = array_replace_recursive(
          $configArray,
          $this->buildCascadeConfig($projectConfigs)
        );
      }
    }

    return $this->createView(new IniPreview($project, $envs, $configArray));
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

  public function renderConfigItems()
  {
    $groupId = $this->getInt("groupId");
    return $this->createView(new ConfigItemsManager($groupId));
  }

  public function postAddingConfigGroup()
  {
    $postData    = $this->request()->postVariables();
    $configGroup = new ConfigurationGroup();
    $configGroup->hydrate($postData);
    $configGroup->saveChanges();

    Redirect::to($this->baseUri() . '/config-groups/' . $postData['projectId'])
      ->now();
  }

  public function postAddingConfigItem()
  {
    $postData = $this->request()->postVariables(
      ['kv', 'groupId'],
      [[], 0]
    );
    foreach($postData['kv'] as $itemId => $data)
    {
      if($data['key'] != '' && $data['value'] != '')
      {
        if($itemId != '*')
        {
          $item                       = new ConfigurationItem($itemId);
          $item->key                  = $data['key'];
          $item->value                = $item->prepValueIn(
            $data['value'],
            $data['type']
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
            $data['value'],
            $data['type']
          );
          $item->type                 = $data['type'];
          $item->configurationGroupId = $postData['groupId'];
          $item->saveChanges();
        }
      }
    }

    Redirect::to(
      $this->baseUri() . '/config-items/' . $postData['groupId']
    )->now();
  }

  public function removeConfigItem()
  {
    $itemId = $this->getInt('itemId');

    //check if item is safe to delete by making sure no other project is using it
    $itemUse = EnvironmentConfigurationItem::collection()->loadWhere(
      ['configuration_item_id' => $itemId]
    );

    $configItem = new ConfigurationItem($itemId);
    if($itemUse->count() == 0 || $this->getStr('force') === 'force')
    {
      $configItem->delete();

      /**
       * @var $row EnvironmentConfigurationItem
       */
      foreach($itemUse as $row)
      {
        $row->delete();
      }

      Redirect::to(
        $this->baseUri() . '/config-items/' . $configItem->configurationGroupId
      )->now();
    }
    else
    {
      $cim            = new ConfigItemsManager($configItem->configurationGroupId);
      $cim->itemInUse = $itemId;
      return $this->createView($cim);
    }
  }

  //safely removes a config item, if it is not used in other projects
  private function _removeConfig($projectId, $envId, $itemId)
  {
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
      '/config-items/:groupId'                                => 'configItems',
      '/adding-config-group'                                  => 'addingConfigGroup',
      '/adding-config-item'                                   => 'addingConfigItem',
      '/remove-config-item/:itemId'                           => 'removeConfigItem',
      '/remove-config-item/:itemId/:force'                    => 'removeConfigItem',
      '/modify-project-config-item'                           => 'modifyProjectConfigItem',
      '/environments'                                         => 'environments',
    );
  }
}