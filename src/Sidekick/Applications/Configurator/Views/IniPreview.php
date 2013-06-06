<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Components\Configure\ConfigWriter;
use Sidekick\Components\Projects\Mappers\Project;

class IniPreview extends ViewModel
{
  /**
   * @var $project Project
   */
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
    $cw = new ConfigWriter();

    $configOutput = new Partial("<h3>%s</h3><pre>%s</pre>");
    foreach($this->envs as $env)
    {
      //make $input safe, in case we don't have any config defined
      //for a particular environment
      $input = isset($this->configArray[$env->name]) ?
        $this->configArray[$env->name] : [];

      ksort($input);
      $configOutput->addElement($env->filename, $cw->buildIni($input, true));
    }

    return new RenderGroup(
      '<h1>Preview</h1>',
      $this->getBreadcrumbs(),
      $configOutput
    );
  }

  public function getBreadcrumbs()
  {
    $parentProject = $this->project->parent();

    $breadcrumbs = new Breadcrumbs();
    $breadcrumbs->addItem('All Projects', $this->baseUri());
    if($parentProject)
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
