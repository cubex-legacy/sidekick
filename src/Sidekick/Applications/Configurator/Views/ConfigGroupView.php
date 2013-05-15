<?php
/**
 * @author: oke.ugwu
 *        Application: Configurator
 */
namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Configurator\Forms\ConfigGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Projects\Mappers\Project;

class ConfigGroupView extends TemplatedViewModel
{
  public $configGroups;
  public $project;
  public $parentProject;

  public function __construct($projectId)
  {
    $this->project = new Project($projectId);
    $this->parentProject = new Project($this->project->parentId);
    $this->configGroups = ConfigurationGroup::collection()->loadWhere(
      [
      'project_id' => $projectId
      ]
    );
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
      $this->project->name . ' <span class="muted">Config Groups</span>'
    );
    return $breadcrumbs;
  }

  public function form()
  {
    $form = new ConfigGroup($this->baseUri() . "/adding-config-group");
    $form->addHiddenElement('projectId', $this->project->id());
    return $form;
  }
}