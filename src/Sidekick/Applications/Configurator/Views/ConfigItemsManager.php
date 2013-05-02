<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Configurator\Views;

use Cubex\Form\Form;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Configure\Enums\ConfigItemType;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationItem;

class ConfigItemsManager extends TemplatedViewModel
{
  protected $_form;
  public $configGroup;
  public $configItems;

  public function __construct($groupId)
  {
    $this->configGroup = new ConfigurationGroup($groupId);

    $this->configItems = ConfigurationItem::collection()->loadWhere(
      ['configuration_group_id' => $groupId]
    );

    $form = new Form('', '/configurator/addingConfigItem');
    $form->setDefaultElementTemplate("{{input}}");
    $form->addHiddenElement('groupId', $groupId);
    foreach($this->configItems as $item)
    {
      $form->addTextElement("kv[$item->id][key]", $item->key);
      $form->addSelectElement(
        "kv[$item->id][type]", [
                           'simple'     => 'Simple',
                           'multiitem'  => 'Multi Item',
                           'multikeyed' => 'Multi Keyed'
                           ]
      );
      $form->addTextElement("kv[$item->id][value]", $item->prepValueOut($item->value, $item->type));
    }
    $form->addTextElement('kv[*][key]', '');
    $form->addSelectElement(
      "kv[*][type]", [
                         'simple'     => 'Simple',
                         'multiitem'  => 'Multi Item',
                         'multikeyed' => 'Multi Keyed'
                         ]
    );
    $form->addTextElement('kv[*][value]', '');
    $form->addSubmitElement('Save', 'submit');

    $this->_form = $form;
  }

  public function form()
  {
    return $this->_form;
  }


}