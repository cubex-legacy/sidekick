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
    )->setOrderBy('id');
  }

  public function addForm()
  {
    $form = new Form(
      'addConfigItem',
    $this->baseUri() . '/adding-config-item'
    );
    $form->setDefaultElementTemplate("{{input}}");
    $form->addHiddenElement('groupId', $this->configGroup->id());
    $form->addTextElement('key', '');
    $form->addSelectElement(
      "type",
      [
      'simple'     => 'Simple',
      'multiitem'  => 'Multi Item',
      'multikeyed' => 'Multi Keyed'
      ]
    );
    $form->addTextElement('value', '');
    $form->addSubmitElement('Add', 'submit');
    return $form;
  }

  public function updateForm()
  {
    $form = new Form(
      'updateConfigItems',
    $this->baseUri() . '/update-config-items'
    );
    $form->setDefaultElementTemplate("{{input}}");
    $form->addHiddenElement('groupId', $this->configGroup->id());
    foreach($this->configItems as $item)
    {
      $form->addTextElement("kv[$item->id][key]", $item->key);
      $form->addSelectElement(
        "kv[$item->id][type]",
        [
        'simple'     => 'Simple',
        'multiitem'  => 'Multi Item',
        'multikeyed' => 'Multi Keyed'
        ],
        $item->type
      );
      $form->addTextElement(
        "kv[$item->id][value]",
        $item->prepValueOut($item->value, $item->type)
      );
    }
    $form->addSubmitElement('Save', 'submit');
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
      $this->project->name . ' Config Groups',
    $this->baseUri() . '/config-groups/' . $this->project->id()
    );

    $breadcrumbs->addItem($this->configGroup->groupName);
    return $breadcrumbs;
  }
}
