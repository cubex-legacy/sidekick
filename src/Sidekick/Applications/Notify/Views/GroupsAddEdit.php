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
use Sidekick\Components\Users\Mappers\User;

class GroupsAddEdit extends TemplatedViewModel
{
  protected $_mode;
  protected $_groupid;
  protected $_group;

  public function __construct($mode, $id, $group)
  {
    $this->_mode = $mode;
    $this->_groupid = $id;
    $this->_group = $group;
  }

  public function getTitle()
  {
    return $this->_mode;
  }

  public function getForm()
  {
    $form = new Form("groupsForm");
    if($this->_mode == "Edit")
    {
      $form->addHiddenElement("id", $this->_groupid);
      $form->addTextElement("groupName", $this->_group->groupName);
      $form->addCheckboxElements(
        "groupUsers[]",
        $this->_group->groupUsers,
        (new OptionBuilder(User::collection()))->getOptions()
      );
    }
    else
    {
      $form->addTextElement("groupName");
      $form->addCheckboxElements(
        "groupUsers[]",
        null,
        (new OptionBuilder(User::collection()))->getOptions()
      );
    }
    $form->addSubmitElement($this->_mode . " Group");
    return new RenderGroup($form);
  }
}
