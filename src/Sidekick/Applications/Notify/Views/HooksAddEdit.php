<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Views;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Notify\Enums\NotifyType;
use Sidekick\Components\Notify\Mappers\EventTypes;
use Sidekick\Components\Notify\Mappers\NotifyGroup;
use Sidekick\Components\Users\Mappers\User;

class HooksAddEdit extends TemplatedViewModel
{

  protected $_mode;
  protected $_hookid;
  protected $_hook;

  public function __construct($mode, $id, $hook)
  {
    $this->_mode = $mode;
    $this->_hookid = $id;
    $this->_hook = $hook;
  }

  public function getTitle()
  {
    return $this->_mode;
  }

  public function getForm()
  {
    $form = new Form("hooksForm");
    if($this->_mode == "Edit")
    {
      $form->addHiddenElement("id", $this->_hookid);
      $form->addSelectElement(
        "eventKey",
        (new OptionBuilder(EventTypes::collection()->getKeyPair(
          "eventKey",
          "eventKey"
        )))->getOptions(),
        $this->_hook->eventKey
      );
      $form->addCheckboxElements(
        "notificationTypes[]",
        $this->_hook->notifyType,
        (new OptionBuilder(new NotifyType()))->getOptions()
      );
      $form->addCheckboxElements(
        "users[]",
        $this->_hook->notifyUsers,
        (new OptionBuilder(User::collection()->getKeyPair(
          "id",
          "display_name"
        )))->getOptions()
      );
      $form->addCheckboxElements(
        "groups[]",
        $this->_hook->notifyGroups,
        (new OptionBuilder(NotifyGroup::collection()->getKeyPair(
          "id",
          "groupName"
        )))->getOptions()
      );
    }
    else
    {
      $form->addHiddenElement("id", "");
      $form->addSelectElement(
        "eventKey",
        (new OptionBuilder(EventTypes::collection()->getKeyPair(
          "eventKey",
          "eventKey"
        )))->getOptions()
      );
      $form->addCheckboxElements(
        "notificationTypes[]",
        null,
        (new OptionBuilder(new NotifyType()))->getOptions()
      );
      $form->addCheckboxElements(
        "users[]",
        null,
        (new OptionBuilder(User::collection()->getKeyPair(
          "id",
          "display_name"
        )))->getOptions()
      );
      $form->addCheckboxElements(
        "groups[]",
        null,
        (new OptionBuilder(NotifyGroup::collection()->getKeyPair(
          "id",
          "groupName"
        )))->getOptions()
      );
    }
    return $form;
  }
}
