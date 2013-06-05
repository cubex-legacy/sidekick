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
use Cubex\View\ViewModel;

class FortifyForm extends ViewModel
{
  protected $_mapper;
  protected $_baseUri;

  public function __construct($mapper, $baseUri)
  {
    $this->_mapper  = $mapper;
    $this->_baseUri = $baseUri;
  }

  protected function _buildForm($id = null)
  {
    $id   = $id ? $id : '';
    $form = new Form("CrudForm", $this->_baseUri . '/' . $id);
    $form->bindMapper(
      $this->_mapper
    );
    $form->getElement('submit')->addAttribute('class', 'btn btn-primary');
    return $form;
  }

  protected function _addArrayElementToForm(Form $form, $elementName)
  {
    if(!empty($this->_mapper->$elementName))
    {
      $i = 0;
      foreach($this->_mapper->$elementName as $arg)
      {
        $form->addElement(
          $elementName . '[' . $i . ']', //not adding an index overwrites it
          FormElement::TEXT,
          $arg,
          [],
          Form::LABEL_NONE
        );
        if($i == 0)
        {
          $form->getElement($elementName . '[' . $i . ']')
          ->setLabel(Strings::titleize($elementName))
          ->setLabelPosition(Form::LABEL_BEFORE);
        }
        $i++;
      }
    }
    else
    {
      $form->addTextElement($elementName . '[0]');
    }
    $form->addElement(
      'add_' . $elementName,
      FormElement::BUTTON,
      ('Add ' . Inflection::singularise(Strings::titleize($elementName)) . ' +'),
      [],
      Form::LABEL_NONE
    );
    $form->getElement('add_' . $elementName)->addAttribute('class', 'btn btn-info')
    ->addAttribute('data-field-to-add', $elementName);
    return $form;
  }

  public function render()
  {
    return $this->_buildForm($this->_mapper->id());
  }
}
