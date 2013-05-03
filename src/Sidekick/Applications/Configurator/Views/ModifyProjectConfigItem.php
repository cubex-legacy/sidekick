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
  public $project;
  public $configGroup;
  public $env;

  public function __construct($projectId, $envId, $itemId)
  {
    $item              = new ConfigurationItem($itemId);
    $this->project     = new Project($projectId);
    $this->configGroup = new ConfigurationGroup($item->configurationGroupId);
    $this->env         = new Environment($envId);

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

      $item->value = $customItem->value;
    }

    $this->_form = new Form('', '/configurator/modify-project-config-item');
    $this->_form->setDefaultElementTemplate("{{input}}");
    $this->_form->addHiddenElement('projectId', $projectId);
    $this->_form->addHiddenElement('envId', $envId);
    $this->_form->addHiddenElement('itemId', $itemId);
    $this->_form->addTextElement('key', $item->key);
    $this->_form->addTextElement(
      'value', $item->prepValueOut($item->value, $item->type)
    );
    $this->_form->addSubmitElement('Update', 'submit');
  }

  public function form()
  {
    return $this->_form;
  }
}