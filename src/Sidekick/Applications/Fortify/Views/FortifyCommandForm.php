<?php
/**
 * @author: davide.argellati
 *        Application: Fortify
 */
namespace Sidekick\Applications\Fortify\Views;

use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Cubex\Helpers\Inflection;
use Cubex\Helpers\Strings;
use Cubex\View\TemplatedViewModel;

class FortifyCommandForm extends TemplatedViewModel
{
  protected $_command;
  /**
   * @var $_form \Cubex\Form\Form
   */
  protected $_form;

  public function __construct($command)
  {
    $this->_command = $command;
  }

  public function form()
  {
    $this->_form = new Form("fortifyCommandForm", $this->baseUri() . '/');
    if($this->_command->id())
    {
      $this->_form = new Form("fortifyCommandForm", $this->baseUri(
        ) . '/' . $this->_command->id());
      $this->_form->addHiddenElement('id', $this->_command->id());
    }

    $this->_form->setDefaultElementTemplate('{{input}}');
    $this->_form->setLabelPosition(Form::LABEL_NONE);

    $this->_form->addTextElement('name', $this->_command->name);
    $this->_form->addTextElement('command', $this->_command->command);
    $this->_form->addTextElement('description', $this->_command->description);
    $this->_form->addTextElement('file_pattern', $this->_command->filePattern);
    $this->_form->addTextElement(
      'file_set_directory',
      $this->_command->fileSetDirectory
    );

    $this->_addArrayElementToForm('success_exit_codes');
    $this->_addArrayElementToForm('args');

    $this->_form->addCheckboxElement(
      'run_on_file_set',
      $this->_command->runOnFileSet,
      true
    );
    $this->_form->addCheckboxElement(
      'cause_build_failure',
      $this->_command->causeBuildFailure,
      true
    );

    $this->_form->addSubmitElement('submit');
    $this->_form->getElement('submit')->addAttribute(
      'class',
      'btn btn-primary'
    );
    return $this->_form;
  }

  protected function _addArrayElementToForm($elementName)
  {
    if(!empty($this->_command->$elementName))
    {
      $i = 0;
      //not adding an index overwrites it
      foreach($this->_command->$elementName as $arg)
      {
        $this->_form->addTextElement(
          $elementName . '[' . $i . ']',
          $arg
        );
        if($i == 0)
        {
          $this->_form->getElement($elementName . '[' . $i . ']')
          ->setLabel(Strings::titleize($elementName))
          ->setLabelPosition(Form::LABEL_BEFORE);
        }
        $i++;
      }
    }
    else
    {
      $this->_form->addTextElement($elementName . '[0]');
    }

    $buttonText = 'Add ' . Inflection::singularise(
        Strings::titleize($elementName)
      );

    $this->_form->addElement(
      'add_' . $elementName,
      FormElement::BUTTON,
      $buttonText,
      [],
      Form::LABEL_NONE
    );

    $this->_form->getElement("add_$elementName")->addAttribute(
      'class',
      'btn btn-info'
    )
    ->addAttribute('data-field-to-add', $elementName);
  }
}
