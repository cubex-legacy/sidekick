<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\ViewModel;
use Sidekick\Components\Configure\ConfigWriter;
use Sidekick\Components\Projects\Mappers\Project;

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
    echo '<h1>Preview</h1>';
    echo $this->getBreadcrumbs();
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

  public function getBreadcrumbs()
  {
    $parentProject = new Project($this->project->parentId);

    $breadcrumbs = new Breadcrumbs();
    $breadcrumbs->addItem('All Projects', $this->baseUri());
    if($parentProject->exists())
    {
      $breadcrumbs->addItem(
        $parentProject->name,
        $this->baseUri() . '/project/' . $parentProject->id()
      );
    }
    $breadcrumbs->addItem(
      $this->project->name . ' <span class="muted">Preview</span>'
    );
    return $breadcrumbs;
  }

}