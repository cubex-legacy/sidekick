<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Views;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Notify\Enums\EventType;
use Sidekick\Components\Notify\Enums\NotifyApplications;
use Sidekick\Components\Projects\Mappers\Project;

class EventsAddEdit extends TemplatedViewModel
{
  private $_mode;
  private $_eventID;
  private $_event;

  public function __construct($mode, $id, $event)
  {
    $this->_mode = $mode;
    $this->_eventID = $id;
    $this->_event = $event;
    $this->requireJs("dynamicTable");
  }

  public function getTitle()
  {
    return $this->_mode;
  }

  public function getForm()
  {
    $form = new Form("eventForm");
    if($this->_mode == "Edit")
    {
      $form->addHiddenElement("id", $this->_eventID);
      $form->addTextElement("eventKey", $this->_event->eventKey);
      $form->addTextareaElement(
        "eventDescription",
        $this->_event->eventDescription
      );
      $form->addCheckboxElements(
        "eventApplications[]",
        $this->_event->eventApplications,
        (new OptionBuilder(new NotifyApplications()))->getOptions()
      );
      $form->addSelectElement(
        "eventType",
        (new OptionBuilder(new EventType()))->getOptions(),
        $this->_event->eventType
      );
    }
    else
    {
      $form->addHiddenElement("id", "");
      $form->addTextElement("eventKey");
      $form->addTextareaElement("eventDescription");
      $form->addCheckboxElements(
        "eventApplications[]",
        null,
        (new OptionBuilder(new NotifyApplications()))->getOptions()
      );
      $form->addSelectElement(
        "eventType",
        (new OptionBuilder(new EventType()))->getOptions()
      );
    }
    return $form;
  }

  public function getRequiredParameters()
  {
    if($this->_mode != "Edit" || $this->_event->eventParams == null)
    {
      return [];
    }
    return $this->_event->eventParams;
  }
}
