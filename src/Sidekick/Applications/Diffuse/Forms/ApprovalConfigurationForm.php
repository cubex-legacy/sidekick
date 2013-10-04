<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 14:46
 */

namespace Sidekick\Applications\Diffuse\Forms;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Enums\Consistency;
use Sidekick\Components\Users\Enums\UserRole;

class ApprovalConfigurationForm extends Form
{
  public $role;
  public $projectId;
  public $consistencyLevel;
  public $required;
  protected $_approvalConfiguration;

  public function __construct($projectId, $role = null, $action = '')
  {
    $this->projectId = $projectId;
    $this->role      = $role;
    $this->_approvalConfiguration       = new ApprovalConfiguration(
      [$this->projectId, $this->role]
    );
    parent::__construct('approvalConfig', $action);
  }

  protected function _configure()
  {
    $this->setDefaultElementTemplate('{{input}}');
    $this->addHiddenElement('projectId', $this->projectId);
    $this->addSelectElement(
      'role',
      (new OptionBuilder(new UserRole))->getOptions(),
      $this->_approvalConfiguration->role
    );

    $this->addSelectElement(
      'consistencyLevel',
      (new OptionBuilder(new Consistency))->getOptions(),
      $this->_approvalConfiguration->consistencyLevel
    );

    $this->addSelectElement(
      'required',
      ['No', 'Yes'],
      $this->_approvalConfiguration->required
    );

    if($this->role == null)
    {
      $this->addSubmitElement('Save');
    }
    else
    {
      $this->addSubmitElement('Update');
      //attach on change events to form field
      //makes an ajax call to update field
      $this->getElement('consistencyLevel')->addAttribute(
        'onchange',
        "updateField(this, $this->projectId, '$this->role')"
      );

      $this->getElement('required')->addAttribute(
        'onchange',
        "updateField(this, $this->projectId, '$this->role')"
      );
    }

    $this->getElement('required')->addAttribute('class', 'input-small');
    $this->getElement('submit')->addAttribute(
      'class',
      'btn btn-primary'
    );
  }
}
