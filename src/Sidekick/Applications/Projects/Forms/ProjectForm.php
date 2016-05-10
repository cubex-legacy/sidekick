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
  public $deploy_base;

  public function __construct($action = '', $id = null)
  {
    $this->id = $id;
    parent::__construct('projectForm', $action);
  }

  protected function _configure()
  {
    $this->get('id')->setType(FormElement::HIDDEN);
    $this->get('name')->setType(FormElement::TEXT)->setRequired(true);
    $this->get('description')->setType(FormElement::TEXTAREA);
    $this->get('deploy_base')->setType(FormElement::TEXT);

    $this->addTextElement('repo[repository_type]');
    $this->addTextElement('repo[name]');
    $this->addTextAreaElement('repo[description]');
    $this->addTextElement('repo[localpath]');
    $this->addTextElement('repo[fetchurl]');
    $this->addTextElement("repo[username]");
    $this->addTextElement("repo[password]");

    $this->get('repo[repository_type]')->setRequired(true)->defaultValue('git');
    $this->get('repo[name]');
    $this->get('repo[description]');
    $this->get('repo[localpath]')->setRequired(true);
    $this->get('repo[fetchurl]')->setRequired(true);
    $this->get("repo[username]")->addAttribute("autocomplete", "off");
    $this->get("repo[password]")->addAttribute("autocomplete", "off");

    if($this->id === null)
    {
      $this->addSubmitElement('Create Project', 'submit');
      $this->getElement('submit')->addAttribute(
        'class',
        'btn btn-success'
      );
    }
    else
    {
      $this->addSubmitElement('Update Project', 'submit');
      $this->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }
  }

  public function hydrate(array $data, $setUnmodified = false, $createAttributes = false, $raw = true)
  {
    parent::hydrate($data, $setUnmodified, $createAttributes, $raw);
    foreach($data['repo'] as $k => $v)
    {
      $this->setData("repo[$k]", $v);
    }
  }

}
