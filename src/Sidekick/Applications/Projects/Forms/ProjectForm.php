<?php
/**
 * Author: oke.ugwu
 * Date: 18/06/13 16:06
 */

namespace Sidekick\Applications\Projects\Forms;

use Cubex\Data\Validator\Validator;
use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Sidekick\Components\Projects\Mappers\Project;

class ProjectForm extends Form
{
  public $id;
  public $name;
  public $parent_id;
  public $description;

  public function __construct($action = '', $id = null)
  {
    $this->id = $id;
    parent::__construct('projectForm', $action);
  }

  protected function _configure()
  {
    $this->bindMapper(new Project($this->id));
    $this->get('id')->setType(FormElement::HIDDEN);
    $this->get('name')->setType(FormElement::TEXT)->setRequired(true);
    $this->get('description')->setType(FormElement::TEXTAREA);
    if($this->id === null)
    {
      $this->addSubmitElement('Create Project', 'submit');
    }
    else
    {
      $this->addSubmitElement('Update Project', 'submit');
    }
  }
}
