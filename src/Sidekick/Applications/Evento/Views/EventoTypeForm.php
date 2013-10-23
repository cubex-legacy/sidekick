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

  public function __construct($eventType)
  {
    $this->_eventType = $eventType;
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
      $this->_form->addSubmitElement('submit');
      $this->_form->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }

    return $this->_form;
  }
}
