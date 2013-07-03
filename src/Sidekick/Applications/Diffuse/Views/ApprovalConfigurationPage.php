<?php
/**
 * Author: oke.ugwu
 * Date: 02/07/13 17:16
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Sidekick\Enums\Consistency;
use Sidekick\Components\Users\Enums\UserRole;

class ApprovalConfigurationPage extends TemplatedViewModel
{
  protected $_form;
  protected $_config;
  public $projectId;

  public function __construct($config, $projectId)
  {
    $this->_config = $config;
    $this->projectId = $projectId;
  }

  public function getConfig()
  {
    return $this->_config;
  }

  public function form()
  {
    if($this->_form == null)
    {
      $this->_form = new Form('approvalConfig', '');
      $this->_form->setDefaultElementTemplate('{{input}}');
      $this->_form->addHiddenElement('projectId', $this->projectId);
      $this->_form->addSelectElement(
        'role',
        (new OptionBuilder(new UserRole))->getOptions()
      );

      $this->_form->addSelectElement(
        'consistencyLevel',
        (new OptionBuilder(new Consistency))->getOptions()
      );

      $this->_form->addRadioElements('required', 0, ['No', 'Yes']);
      $this->_form->addSubmitElement('Save');

      $this->_form->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }

    return $this->_form;
  }
}
