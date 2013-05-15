<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Views;

use Cubex\Form\Form;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Configure\Mappers\CustomConfigurationItem;
use Sidekick\Components\Configure\Mappers\Environment;
use Sidekick\Components\Configure\Mappers\EnvironmentConfigurationItem;
use Sidekick\Components\Projects\Mappers\Project;

class ModifyProjectConfigItem extends TemplatedViewModel
{
  public $item;
  public $project;
  public $parentProject;
  public $configGroup;
  public $env;

  public function __construct($projectId, $envId, $itemId)
  {
    $this->item          = new ConfigurationItem($itemId);
    $this->project       = new Project($projectId);
    $this->parentProject = new Project($this->project->parentId);
    $this->env           = new Environment($envId);
    $this->configGroup   = new ConfigurationGroup(
      $this->item->configurationGroupId
    );

    $projectConfig = EnvironmentConfigurationItem::collection()
      ->loadOneWhere(
        [
        'project_id'            => $projectId,
        'environment_id'        => $envId,
        'configuration_item_id' => $itemId
        ]
      );

    if($projectConfig->customItemId !== null)
    {
      $customItem = CustomConfigurationItem::collection()->loadOneWhere(
        ['id' => $projectConfig->customItemId]
      );

      $this->item->value = $customItem->value;
    }
  }

  public function form()
  {
    $form = new Form(
      'modifyProjectConfigItem',
      $this->baseUri() . '/modify-project-config-item'
    );
    $form->setDefaultElementTemplate("{{input}}");
    $form->addHiddenElement('projectId', $this->project->id());
    $form->addHiddenElement('envId', $this->env->id());
    $form->addHiddenElement('itemId', $this->item->id());
    $form->addTextElement('key', $this->item->key);
    $form->addTextElement(
      'value',
      $this->item->prepValueOut($this->item->value, $this->item->type)
    );
    $form->addSubmitElement('Update', 'submit');
    return $form;
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
      $this->project->name . ' Configure',
      $this->baseUri() . '/project-configs/' . $this->project->id()
    );

    $breadcrumbs->addItem(
      $this->configGroup->groupName . ' <span class="muted">' . ucwords(
        $this->env->name
      ) . '</span>'
    );

    return $breadcrumbs;
  }
}