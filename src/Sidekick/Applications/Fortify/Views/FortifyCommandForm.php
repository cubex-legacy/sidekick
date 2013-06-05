<?php
/**
 * @author: davide.argellati
 *        Application: Fortify
 */
namespace Sidekick\Applications\Fortify\Views;

use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Cubex\Helpers\Strings;

class FortifyCommandForm extends FortifyForm
{

  protected function _buildForm($id = null)
  {
    $id   = $id ? $id : '';
    $form = new Form("CrudForm", $this->_baseUri . '/' . $id);
    $form->addAttribute('class', 'pull-left');
    $form->addHiddenElement('id', $id);
    $form->addTextElement('command', $this->_mapper->command);
    $form->addTextElement('name', $this->_mapper->name);
    $form->addTextElement('description', $this->_mapper->description);
    $form->addTextElement(
      'file_set_directory',
      $this->_mapper->fileSetDirectory
    );
    $form->addTextElement('file_pattern', $this->_mapper->filePattern);

    $this->_addArrayElementToForm($form, 'success_exit_codes');
    $this->_addArrayElementToForm($form, 'args');

    $form->addElement('cause_build_failure', FormElement::CHECKBOX);
    $form->getElement('cause_build_failure')
    ->setChecked($this->_mapper->causeBuildFailure)
    ->setRenderTemplate("<dd class='checkbox'>{{input}}</dd>");

    $form->addElement('run_on_file_set', FormElement::CHECKBOX);
    $form->getElement('run_on_file_set')
    ->setChecked($this->_mapper->runOnFileSet)
    ->setRenderTemplate("<dd class='checkbox'>{{input}}</dd>");

    $form->addSubmitElement('submit');
    $form->getElement('submit')->addAttribute('class', 'btn btn-primary');
    return $form;
  }

  public function render()
  {
    return $this->_buildForm($this->_mapper->id());
  }
}
