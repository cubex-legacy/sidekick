<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento\Views;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\Helpers\DateTimeHelper;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Evento\Enums\Resolution;

class EventoView extends TemplatedViewModel
{
  protected $_event;
  protected $_updates;
  protected $_form;

  public function __construct($event, $updates)
  {
    $this->_event   = $event;
    $this->_updates = $updates;
  }

  /**
   * @return \Sidekick\Components\Evento\Mappers\Event
   */
  public function event()
  {
    return $this->_event;
  }

  public function getEventDuration()
  {
    $start = strtotime($this->event()->openedAt);
    $end   = strtotime($this->event()->closedAt);
    if($this->event()->closedAt === null)
    {
      $end = time();
    }

    $diff = $end - $start;
    return DateTimeHelper::secondsToTime($diff);
  }

  /**
   * @return \Sidekick\Components\Evento\Mappers\EventUpdate[]
   */
  public function getUpdates()
  {
    return $this->_updates;
  }

  public function form()
  {
    if($this->_form === null)
    {
      $this->_form = new Form(
        "eventoUpdateForm",
        $this->baseUri() . '/' . $this->event()->id()
      );

      $this->_form->setDefaultElementTemplate('{{input}}');
      $this->_form->setLabelPosition(Form::LABEL_NONE);

      $this->_form->addTextareaElement('description');
      $this->_form->getElement('description')->addAttribute(
        'class',
        'input-xxlarge'
      );
      $this->_form->getElement('description')->addAttribute('rows', 3);
      $this->_form->getElement('description')->addAttribute(
        'style',
        'width:98.5%'
      );

      $this->_form->addSelectElement(
        "resolution",
        (new OptionBuilder(new Resolution()))->getOptions()
      );

      $this->_form->addSubmitElement('Send Update', 'submit');
      $this->_form->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary pull-right'
      );
    }

    return $this->_form;
  }
}
