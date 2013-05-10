<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\ViewModel;
use Sidekick\Components\Configure\ConfigWriter;

class IniPreview extends ViewModel
{
  public $project;
  public $envs;
  public $configArray;

  public function __construct($project, $envs, $configArray)
  {
    $this->project     = $project;
    $this->envs        = $envs;
    $this->configArray = $configArray;
  }

  public function render()
  {
    echo "<h1>{$this->project->name}</h1>";
    $cw = new ConfigWriter();
    foreach($this->envs as $env)
    {
      //make input safe, incase we don't have any config defined
      //for a particular environment
      $input = isset($this->configArray[$env->name]) ?
        $this->configArray[$env->name] : [];

      ksort($input);

      echo "<h3>$env->filename</h3>";
      echo "<pre>";
      $cw->buildIni($input, true);
      echo "</pre>";
    }
  }
}