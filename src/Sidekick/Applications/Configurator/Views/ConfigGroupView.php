<?php
/**
 * @author: oke.ugwu
 * Application: Configurator
 */
namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\ViewModel;
use Sidekick\Applications\Configurator\Forms\ConfigGroup;

class ConfigGroupView extends ViewModel
{
  protected $_form;

  public function __construct($projectId)
  {
    $this->_form = new ConfigGroup("/configurator/adding-config-group");
    $this->_form->addHiddenElement('projectId', $projectId);
  }


  public function form()
  {
    return $this->_form;
  }

  public function render()
  {
    echo "<h1>Add Config Group</h1>";
    echo $this->form();
  }

}