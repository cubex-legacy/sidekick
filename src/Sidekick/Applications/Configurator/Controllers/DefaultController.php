<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Controllers;

use Cubex\Form\Form;
use Cubex\Facade\Redirect;
use Sidekick\Applications\Configurator\Views\ConfigGroupView;
use Sidekick\Applications\Configurator\Views\ConfigItemsManager;
use Sidekick\Applications\Configurator\Views\IniPreview;
use Sidekick\Applications\Configurator\Views\ModifyProjectConfigItem;
use Sidekick\Applications\Configurator\Views\ProjectConfigurator;
use Sidekick\Applications\Configurator\Views\ProjectList;
use Sidekick\Components\Configure\Helpers\ConfigHelper;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Configure\Mappers\CustomConfigurationItem;
use Sidekick\Components\Configure\Mappers\Environment;
use Sidekick\Components\Configure\Mappers\EnvironmentConfigurationItem;
use Sidekick\Components\Phuse\Mappers\Package;
use Sidekick\Components\Projects\Mappers\Project;

class DefaultController extends ConfiguratorController
{
  public function renderIndex()
  {
    $projectId     = $this->getInt('projectId');
    $parentProject = null;

    $projects = Project::getProjects($projectId);
    if($projectId !== null)
    {
      $parentProject = new Project($projectId);
    }

    $subProjects  = Project::getSubProjectsCount();
    $configGroups = ConfigurationGroup::getConfigGroupsCount();

    $pl = $this->createView(new ProjectList());
    $pl->setProjects($projects)
    ->setSubProjects($subProjects)
    ->setConfigGroups($configGroups)
    ->setParentProject($parentProject);

    return $pl;
  }

  public function renderConfigGroups()
  {
    $projectId = $this->getInt('projectId');
    return $this->createView(new ConfigGroupView($projectId));
  }

  public function renderProjectConfigs()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId');

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
    $projectId   = $this->getInt('projectId');
    $project     = new Project($projectId);
    $envs        = Environment::collection()->loadAll();
    $configArray = (new ConfigHelper())->getConfigArray($projectId, $envs);
    return $this->createView(new IniPreview($project, $envs, $configArray));
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
      ['key', 'type', 'value', 'groupId'],
      ['', '', '', 0]
    );

    if(($postData['key'] != '' && $postData['value'] != '')
      || ($postData['type'] == 'multiitem' && $postData['key'] != '')
    )
    {

      $item                       = new ConfigurationItem();
      $item->key                  = $postData['key'];
      $item->value                = $item->prepValueIn(
        $postData['value'],
        $postData['type']
      );
      $item->type                 = $postData['type'];
      $item->configurationGroupId = $postData['groupId'];
      $item->saveChanges();
    }
    else
    {
      $error = true;
    }

    $msg       = new \stdClass();
    $msg->type = 'error';
    $msg->text = 'Config Item could not be added';
    if(!$error)
    {
      $msg->type = 'success';
      $msg->text = 'New Config Item Added';
    }

    Redirect::to(
      $this->baseUri() . '/config-items/' . $postData['groupId']
    )->with('msg', $msg)->now();
  }

  public function postUpdateConfigItems()
  {
    $postData = $this->request()->postVariables(
      ['kv', 'groupId'],
      [[], 0]
    );

    //here we save as we go, so if user has made multiple changes to config
    //they don't lose it all because of a single error
    $error = false;
    foreach($postData['kv'] as $itemId => $data)
    {
      if(!isset($data['key']) || !isset($data['value']) || !isset($data['type']))
      {
        $error = true;
      }
      elseif(($data['key'] != '' && $data['value'] != '')
        || ($data['type'] == 'multiitem' && $data['key'] != '')
      )
      {
        $item                       = new ConfigurationItem($itemId);
        $item->key                  = $data['key'];
        $item->value                = $item->prepValueIn(
          $data['value'],
          $data['type']
        );
        $item->type                 = $data['type'];
        $item->configurationGroupId = $postData['groupId'];
        $item->saveChanges();
      }
      else
      {
        $error = true;
      }
    }

    $msg       = new \stdClass();
    $msg->type = 'error';
    $msg->text = 'Config Items could not be saved';
    if(!$error)
    {
      $msg->type = 'success';
      $msg->text = 'Config Items Saved';
    }

    Redirect::to(
      $this->baseUri() . '/config-items/' . $postData['groupId']
    )->with('msg', $msg)->now();
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
      '/update-config-items'                                  => 'updateConfigItems',
      '/remove-config-item/:itemId'                           => 'removeConfigItem',
      '/remove-config-item/:itemId/:force'                    => 'removeConfigItem',
      '/modify-project-config-item'                           => 'modifyProjectConfigItem',
      '/environments'                                         => 'environments',
    );
  }
}
