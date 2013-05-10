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
  protected $_form;
  public $configGroups;
  public $project;
  public $parentProject;

  public function __construct($projectId)
  {
    $this->project       = new Project($projectId);
    $this->parentProject = new Project($this->project->parentId);
    $this->configGroups  = ConfigurationGroup::collection()->loadWhere(
      [
      'project_id' => $projectId
      ]
    );
  }

  public function form()
  {
    $this->_form = new ConfigGroup($this->baseUri() . "/adding-config-group");
    $this->_form->addHiddenElement('projectId', $this->project->id());
    return $this->_form;
  }
}