<?php
/**
 * @author  oke.ugwu 
 */

namespace Sidekick\Applications\Configurator\Forms;


use Cubex\Data\Validator\Validator;
use Cubex\Form\Form;
use Cubex\Form\FormElement;

class ConfigGroup extends Form
{
  public $groupName;
  public $entry;
  public $id = null;

  public function __construct($action = '')
  {
    parent::__construct("AddConfigGroupForm", $action);
  }

  protected function _configure()
  {
    $this->setDefaultElementTemplate('<dt>{{label}}</dt><dd>{{input}}</dd>');

    $this->get("groupName")->setType(FormElement::TEXT)
    ->addValidator(Validator::VALIDATE_NOTEMPTY);

    $this->get("entry")->setType(FormElement::TEXT)
    ->addValidator(Validator::VALIDATE_NOTEMPTY);

    $this->get("id")->setType(FormElement::HIDDEN);

    if($this->id === null)
    {
      $this->addSubmitElement("Save", "Save");
    }
    else
    {

      $this->addSubmitElement("Update", "Update");
    }

  }
}