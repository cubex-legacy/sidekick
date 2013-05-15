<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Cli\Configurator;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Foundation\Config\ConfigGroup;
use Cubex\Helpers\Inflection;
use Cubex\Mapper\Database\PivotMapper;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Text\TextTable;
use Psr\Log\LogLevel;
use Sidekick\Components\Configure\ConfigWriter;
use Sidekick\Components\Configure\Enums\ConfigItemType;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Configure\Mappers\Environment;
use Sidekick\Components\Configure\Mappers\ProjectConfigurationItem;
use Sidekick\Components\Projects\Mappers\Project;

class Test extends CliCommand
{
  /**
   * @return int
   */
  protected $_echoLevel = LogLevel::WARNING;
  protected $_autoLog = true;

  public $envs = array(
    1 => "defaults",
    "development",
    "live",
    "stage"
  );

  public function init()
  {
    parent::init();
    //$x = new DebuggerBundle();
    //$x->init();
    //$this->getProjectConfig(1);
  }

  /**
   * @return int
   */
  public function execute()
  {
    // TODO: Implement execute() method.
  }


  public function getProjectConfig($projectId, $envName = null)
  {
    if(!in_array($envName, $this->envs))
    {
      $envName = CUBEX_ENV;
    }

    $projectConfigs = ProjectConfigurationItem::conn()->getRows(
      "SELECT * FROM `configure_project_configuration_items`
      WHERE `project_id` = $projectId"
    );

    $configArray = array();
    foreach($projectConfigs as $config)
    {
      $project = new Project($config->project_id);
      $item    = new ConfigurationItem($config->configuration_item_id);
      $group   = new ConfigurationGroup($item->configurationGroupId);
      $env     = $this->envs[$config->environment_id];

      $configArray[$project->name][$env][$group->entry][$item->key] = is_object($item->value)? (array)$item->value : $item->value;
    }

    $cw = new ConfigWriter();
    echo ";Project Name: $project->name".PHP_EOL;
    echo $cw->buildIni($configArray[$project->name][$envName], true);
  }

  public function buildEnvs()
  {
    foreach($this->envs as $k => $envString)
    {
      $env           = new Environment($k);
      $env->name     = $envString;
      $env->filename = strtolower($envString . '.ini');
      $env->saveChanges();
    }
  }


  public function buildTestData()
  {

  }
}