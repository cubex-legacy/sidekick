<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 14:46
 */

namespace Sidekick\Applications\Diffuse\Forms;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Sidekick\Enums\Consistency;
use Sidekick\Components\Users\Enums\UserRole;

class ApprovalConfigurationForm extends Form
{
  public $role;
  public $projectId;
  public $consistencyLevel;
  public $required;

  public function __construct($projectId, $role = null, $action = '')
  {
    $this->role      = $role;
    $this->projectId = $projectId;
    parent::__construct('approvalConfig', $action);
  }

  protected function _configure()
  {
    $ac = new ApprovalConfiguration([$this->projectId, $this->role]);

    $this->setDefaultElementTemplate('{{input}}');
    $this->addHiddenElement('projectId', $this->projectId);
    $this->addSelectElement(
      'role',
      (new OptionBuilder(new UserRole))->getOptions(),
      $ac->role
    );

    $this->addSelectElement(
      'consistencyLevel',
      (new OptionBuilder(new Consistency))->getOptions(),
      $ac->consistencyLevel
    );

    $this->addRadioElements('required', $ac->required, ['No', 'Yes']);
    if($this->role == null)
    {
      $this->addSubmitElement('Save');
    }
    else
    {
      $this->addSubmitElement('Update');
    }

    $this->getElement('submit')->addAttribute(
      'class',
      'btn btn-primary'
    );
  }
}
