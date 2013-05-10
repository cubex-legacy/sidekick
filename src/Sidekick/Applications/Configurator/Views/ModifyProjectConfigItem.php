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
  protected $_form;
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
    $this->configGroup   = new ConfigurationGroup($this->item->configurationGroupId);
    $this->env           = new Environment($envId);

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
    $this->_form = new Form(
      'modifyProjectConfigItem',
      $this->baseUri() . '/modify-project-config-item'
    );
    $this->_form->setDefaultElementTemplate("{{input}}");
    $this->_form->addHiddenElement('projectId', $this->project->id());
    $this->_form->addHiddenElement('envId', $this->env->id());
    $this->_form->addHiddenElement('itemId', $this->item->id());
    $this->_form->addTextElement('key', $this->item->key);
    $this->_form->addTextElement(
      'value',
      $this->item->prepValueOut($this->item->value, $this->item->type)
    );
    $this->_form->addSubmitElement('Update', 'submit');
    return $this->_form;
  }
}