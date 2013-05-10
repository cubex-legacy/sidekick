<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Views;

use Cubex\Form\Form;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;
use Sidekick\Components\Projects\Mappers\Project;

class ConfigItemsManager extends TemplatedViewModel
{
  protected $_form;
  public $configGroup;
  public $project;
  public $parentProject;
  public $configItems;
  public $itemInUse = false;

  public function __construct($groupId)
  {
    $this->configGroup   = new ConfigurationGroup($groupId);
    $this->project       = new Project($this->configGroup->projectId);
    $this->parentProject = new Project($this->project->parentId);

    $this->configItems = ConfigurationItem::collection()->loadWhere(
      ['configuration_group_id' => $groupId]
    );
  }

  public function form()
  {
    $this->_form = new Form(
      'addConfigItem',
      $this->baseUri() . '/adding-config-item'
    );
    $this->_form->setDefaultElementTemplate("{{input}}");
    $this->_form->addHiddenElement('groupId', $this->configGroup->id());
    foreach($this->configItems as $item)
    {
      $this->_form->addTextElement("kv[$item->id][key]", $item->key);
      $this->_form->addSelectElement(
        "kv[$item->id][type]",
        [
        'simple'     => 'Simple',
        'multiitem'  => 'Multi Item',
        'multikeyed' => 'Multi Keyed'
        ],
        $item->type
      );
      $this->_form->addTextElement(
        "kv[$item->id][value]",
        $item->prepValueOut($item->value, $item->type)
      );
    }
    $this->_form->addTextElement('kv[*][key]', '');
    $this->_form->addSelectElement(
      "kv[*][type]",
      [
      'simple'     => 'Simple',
      'multiitem'  => 'Multi Item',
      'multikeyed' => 'Multi Keyed'
      ]
    );
    $this->_form->addTextElement('kv[*][value]', '');
    $this->_form->addSubmitElement('Save', 'submit');
    return $this->_form;
  }
}