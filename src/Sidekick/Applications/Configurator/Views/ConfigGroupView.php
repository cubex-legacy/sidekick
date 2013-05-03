<?php
/**
 * @author: oke.ugwu
 * Application: Configurator
 */
namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Configurator\Forms\ConfigGroup;
use Sidekick\Components\Configure\Mappers\ConfigurationGroup;

class ConfigGroupView extends TemplatedViewModel
{
  protected $_form;
  public $configGroups;

  public function __construct($projectId)
  {
    $this->_form = new ConfigGroup("/configurator/adding-config-group");
    $this->_form->addHiddenElement('projectId', $projectId);

    $this->configGroups = ConfigurationGroup::collection()->loadWhere(
      [
      'project_id' => $projectId
      ]
    );
  }


  public function form()
  {
    return $this->_form;
  }
}