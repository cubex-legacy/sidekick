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
use Sidekick\Components\Evento\Mappers\EventType;

class EventoForm extends TemplatedViewModel
{
  /**
   * @var \Sidekick\Components\Evento\Mappers\Event
   */
  protected $_event;
  protected $_form;

  public function __construct($event)
  {
    $this->_event = $event;
  }

  public function form()
  {
    if($this->_form === null)
    {
      if($this->_event->exists())
      {
        $this->_form = new Form(
          "eventoForm",
          $this->baseUri() . '/' . $this->_event->id()
        );
        $this->_form->addHiddenElement('id', $this->_event->id());
      }
      else
      {
        $this->_form = new Form("eventoForm", $this->baseUri() . '/');
      }

      $this->_form->setDefaultElementTemplate('{{input}}');
      $this->_form->setLabelPosition(Form::LABEL_NONE);

      $this->_form->addTextElement('name', $this->_event->name);
      $this->_form->getElement('name')->setRequired(true);
      $this->_form->getElement('name')->addAttribute('class', 'input-xxlarge');
      $this->_form->addTextareaElement(
        'description',
        $this->_event->description
      );
      $this->_form->getElement('description')->addAttribute(
        'class',
        'input-xxlarge'
      );
      $this->_form->getElement('description')->addAttribute('rows', 5);
      $this->_form->addSelectElement(
        "severity",
        (new OptionBuilder(new Severity()))->getOptions(),
        $this->_event->severity
      );

      $this->_form->addSelectElement(
        "eventTypeId",
        (new OptionBuilder(EventType::collection()))->getOptions(),
        $this->_event->severity
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
