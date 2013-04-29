<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Controllers;

use Cubex\Form\Form;
use Cubex\Facade\Redirect;
use Cubex\Mapper\Database\RecordCollection;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Applications\Configurator\Views\ConfigGroupView;
use Sidekick\Components\Configure\ConfigWriter;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Configure\Mappers\CustomConfigurationItem;
use Sidekick\Components\Configure\Mappers\Environment;
use Sidekick\Components\Configure\Mappers\EnvironmentConfigurationItem;
use Sidekick\Components\Configure\Mappers\ProjectConfigurationItem;
use Sidekick\Components\Projects\Mappers\Project;

class DefaultController extends BaseControl
{
  public function renderIndex()
  {
    echo "<h1>Projects</h1>";
    $projectCollection = new RecordCollection(new Project());
    $projects          = $projectCollection->loadAll();
    foreach($projects as $project)
    {
      echo '<li><a href="/configurator/project-configs/' .
      $project->id . '">' . $project->name . '</a></li>';
    }

    echo "<h1>Config Groups</h1>";
    echo "<span><a href='/configurator/add-config-group'>Add New Group</a>";
    $configGroups = ConfigurationGroup::collection()->loadAll();
    foreach($configGroups as $group)
    {
      echo '<li><a href="/configurator/config-items/' . $group->id . '/' .
      $group->groupName . '">' . $group->groupName . '</a></li>';
    }
  }

  public function renderProjectConfigs()
  {
    $projectId = $this->getInt('projectId');
    $project   = new Project($projectId);
    $envId     = $this->getInt('envId', 0);
    $env       = new Environment($envId);

    $envs = array(
      1 => "defaults",
      "development",
      "live",
      "stage"
    );

    $configGroups = ConfigurationGroup::collection()
    ->loadAll()->setOrderBy("group_name");

    echo "<h1><a href='/configurator/project-configs/$projectId'>$project->name</a>";
    echo ($env->exists()) ? ' > ' . ucwords($env->name) : "";
    echo "</h1>";
    foreach($envs as $enviId => $envName)
    {
      echo "<p style='float:left; margin-right:10px;'>
      <a href='/configurator/project-configs/$projectId/$enviId'>";
      echo ucwords($envName);
      echo "</a></p>";
    }
    echo "<div style='clear:both;'></div>";
    echo "<em>Add config items on project level</em>";

    echo "<div>";

    foreach($configGroups as $group)
    {
      $configItems = ConfigurationItem::collection()
      ->loadWhere(['configuration_group_id' => $group->id]);

      echo "<div style='width:400px; height:200px; padding:10px; float:left;
      border:1px solid #ccc; border-radius: 5px;
      margin-bottom:5px; margin-right:5px;'>";
      echo "<p><b>$group->groupName</b></p>";
      if($configItems->count() > 0)
      {
        foreach($configItems as $item)
        {
          echo "<p style='width:100%;word-wrap:break-word;margin:0;'><small>";
          echo "[<a href='/configurator/add-project-config-item/" .
          $projectId . "/" . $envId . "/" .
          $item->id . "' style='color:green; font-weight:bold; font-size:12px;'>+</a>] ";

          echo "$item->key = <b>$item->value</b></small></p>";
        }
      }
      echo "</div>";
    }
    echo "</div>";
    echo "<div style='clear:both;'></div>";

    echo "<a class='btn'
    href='/configurator/build-ini/$projectId/$envId'>Build $env->filename</a>";

    //show actual config
    $this->_displayConfig($projectId, $envId);
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

    $project     = new Project($projectId);
    $item        = new ConfigurationItem($itemId);
    $configGroup = new ConfigurationGroup($item->configurationGroupId);
    $env         = new Environment($envId);

    $projectConfig = EnvironmentConfigurationItem::collection()
    ->loadOneWhere(
      [
      'project_id'            => $projectId,
      'environment_id'        => $envId,
      'configuration_item_id' => $itemId
      ]
    );

    $item = new ConfigurationItem($projectConfig->configurationItemId);

    echo "<h1>$project->name > ";
    echo ($env->exists()) ? ucwords($env->name) . ' >' : "";
    echo " $configGroup->groupName</h1>";
    echo "<form method='post' action='/configurator/modifyProjectConfigItem'>";
    echo "<input type='hidden' name='projectId' value='$projectId'>";
    echo "<input type='hidden' name='envId' value='$envId'>";
    echo "<input type='hidden' name='itemId' value='$itemId'>";
    echo "<table><tr><td>Key</td><td>Value</td></tr>";
    echo "<tr>";
    echo "<td><input type='text' name='key' value='$item->key' readonly></td>";
    echo "<td><input type='text' name='value' value='$item->value'></td>";
    echo "</tr>";
    echo '<tr><td colspan="2"><input type="submit" value="Update" /></td></tr>';
    echo "</table>";
  }

  public function postModifyProjectConfigItem()
  {
    $postData = $this->request()->postVariables();
    if(isset($postData['key']))
    {
      $item        = new ConfigurationItem($postData['itemId']);
      $item->value = json_encode($postData['value']);

      $key = $postData['key'];
      if($item->getAttribute('value')->isModified())
      {
        echo "key <b>$key</b> was modified<br/>";

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
          $customItem->value  = $postData['value'];
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
      else
      {
        echo "key <b>$key</b> did not change<br/>";
      }
    }
  }

  public function buildIni()
  {
    $projectId = $this->getInt('projectId');
    $envId     = $this->getInt('envId');

    //load config in cascade fashion
    $cascade = ($envId > 0) ? [
      0,
      1,
      $envId
    ] : [0];
    $cascade = array_unique($cascade);

    $configArray = [];
    foreach($cascade as $level)
    {
      $projectConfigs = EnvironmentConfigurationItem::collection()
      ->loadWhere(
        [
        'project_id'     => $projectId,
        'environment_id' => $level,
        ]
      );

      $configArray = array_merge(
        $configArray, $this->buildCascadeConfig($projectConfigs)
      );
    }

    ksort($configArray);

    $env = new Environment($envId);

    echo "<h1>$env->filename</h1>";
    $cw = new ConfigWriter();
    echo "<pre>";
    $cw->buildIni($configArray, true);
  }

  public function buildCascadeConfig($projectConfigs)
  {
    $configArray = array();
    foreach($projectConfigs as $config)
    {
      $item  = new ConfigurationItem($config->configurationItemId);
      $group = new ConfigurationGroup($item->configurationGroupId);

      //check if custom value assign
      if($config->customItemId !== null)
      {
        //override value before displaying
        $customItem  = new CustomConfigurationItem($config->customItemId);
        $item->value = $customItem->value;
      }

      $configArray[$group->entry][$item->key] = is_object(
        $item->value
      ) ? (array)$item->value : $item->value;
    }

    return $configArray;
  }


  public function renderAddConfigGroup()
  {
    return new ConfigGroupView();
  }

  public function renderConfigItems()
  {
    $groupID   = $this->getInt("groupID");
    $groupName = $this->getStr("groupName");
    echo "<h1>Config Items ($groupName)</h1>";

    $configItems = ConfigurationItem::collection()->loadWhere(
      "configuration_group_id=$groupID"
    );

    echo "<table><tr><td>Key</td><td>Value</td></tr>";
    foreach($configItems as $item)
    {
      echo "<tr>";
      echo "<td><input type='text' value='$item->key'></td>";
      echo "<td><input type='text' value='" . json_encode(
        $item->value
      ) . "'></td>";
      echo "</tr>";
    }
    echo "</table>";
    echo '<form action="/configurator/addingConfigItem" method="post">';
    echo '<input type="hidden" name="configurationGroupID" value="' . $groupID . '" >';
    echo "<table>";
    echo "<tr>";
    echo "<td><input type='text' name='key' value=''></td>";
    echo "<td><input type='text' name='value' value=''></td>";
    echo "<td><input type='submit' value='Add'></td>";
    echo "</tr>";
    echo "</form>";
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
    $postData          = $this->request()->postVariables();
    $postData['value'] = json_encode($postData['value']);
    $configItem        = new ConfigurationItem();
    $configItem->hydrate($postData);
    $configItem->saveChanges();

    Redirect::to(
      '/configurator/config-items/' . $configItem->configurationGroupId
    )->now();
  }

  private function _displayConfig($projectId, $envId)
  {
    $projectConfigs = EnvironmentConfigurationItem::collection()
    ->loadWhere(
      [
      'project_id'     => $projectId,
      'environment_id' => $envId,
      ]
    );

    $groupedConfig = array();
    $configArray   = array();
    foreach($projectConfigs as $config)
    {
      $item  = new ConfigurationItem($config->configurationItemId);
      $group = new ConfigurationGroup($item->configurationGroupId);
      $env   = new Environment($config->environmentId);
      $env   = ($env->exists()) ? $env->name : 'global';

      //check if custom value assign
      if($config->customItemId !== null)
      {
        //override value before displaying
        $customItem  = new CustomConfigurationItem($config->customItemId);
        $item->value = $customItem->value;
      }

      $configArray[$env][$group->groupName][$item->key] = is_object(
        $item->value
      ) ? (array)$item->value : $item->value;

      $groupedConfig[$group->groupName][$item->key] = $item;
    }
    ksort($groupedConfig);

    echo "<hr/>";
    foreach($groupedConfig as $groupName => $items)
    {
      echo "<div style='margin-bottom:5px;'>";
      echo "<p style='margin:0;'><b>$groupName</b></p>";

      foreach($items as $itemObj)
      {
        echo "<p style='width:100%;word-wrap:break-word;margin:0;'><small>";
        echo "[<a href='/configurator/modify-project-config-item/" .
        $projectId . "/" . $envId . "/" .
        $itemObj->id . "' style='color:blue; font-weight:bold; font-size:12px;'>...</a>] ";

        echo "[<a href='/configurator/remove-project-config-item/" .
        $projectId . "/" . $envId . "/" .
        $itemObj->id . "' style='color:red; font-weight:bold; font-size:12px;'>x</a>] ";

        echo "$itemObj->key = <b>$itemObj->value</b></small></p>";
      }

      echo "</div>";
    }
  }

  public function getRoutes()
  {
    return array(
      '/configurator/project-configs/:projectId'                           => 'projectConfigs',
      '/configurator/build-ini/:projectId/:envId'                          => 'buildIni',
      '/configurator/project-configs/:projectId/:envId'                    => 'projectConfigs',
      '/configurator/add-project-config-item/:projectId/:envId/:itemId'    => 'addProjectConfigItem',
      '/configurator/remove-project-config-item/:projectId/:envId/:itemId' => 'removeProjectConfigItem',
      '/configurator/modify-project-config-item/:projectId/:envId/:itemId' => 'modifyProjectConfigItem',
      '/configurator/add-config-group'                                     => 'addConfigGroup',
      '/configurator/config-items/:groupID/:groupName'                     => 'configItems',
      '/configurator/config-items/:groupID'                                => 'configItems',
      '/configurator/addingConfigGroup'                                    => 'addingConfigGroup',
      '/configurator/addingConfigItem'                                     => 'addingConfigItem',
      '/configurator/modifyProjectConfigItem'                              => 'modifyProjectConfigItem',
    );
  }
}