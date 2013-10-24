<?php
/**
 * Author: oke.ugwu
 * Date: 23/10/13 11:45
 */

namespace Sidekick\Applications\Evento\Views;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Enums\Severity;

class EventoTypeForm extends TemplatedViewModel
{
  /**
   * @var \Sidekick\Components\Evento\Mappers\EventType
   */
  protected $_eventType;
  protected $_form;
  protected $_title;

  public function __construct($eventType)
  {
    $this->_eventType = $eventType;
    $this->_title     = "New Event Type";
    if($this->_eventType->exists())
    {
      $this->_title = "Edit Event Type";
    }
  }

  public function title()
  {
    return $this->_title;
  }

  public function form()
  {
    if($this->_form === null)
    {
      if($this->_eventType->exists())
      {
        $this->_form = new Form(
          "eventoTypeForm",
          $this->baseUri() . '/' . $this->_eventType->id()
        );
        $this->_form->addHiddenElement('id', $this->_eventType->id());
      }
      else
      {
        $this->_form = new Form("eventoTypeForm", $this->baseUri() . '/');
      }

      $this->_form->setDefaultElementTemplate('{{input}}');
      $this->_form->setLabelPosition(Form::LABEL_NONE);

      $this->_form->addTextElement('name', $this->_eventType->name);
      $this->_form->getElement('name')->setRequired(true);
      $this->_form->getElement('name')->addAttribute('class', 'input-xxlarge');
      $this->_form->addTextareaElement(
        'description',
        $this->_eventType->description
      );
      $this->_form->getElement('description')->addAttribute(
        'class',
        'input-xxlarge'
      );
      $btnText = ($this->_eventType->exists()) ? "Update" : "Create";
      $this->_form->addSubmitElement($btnText, 'submit');
      $this->_form->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }

    return $this->_form;
  }
}
