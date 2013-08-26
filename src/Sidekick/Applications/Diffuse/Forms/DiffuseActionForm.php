<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Forms;

use Cubex\Data\Validator\Validator;
use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Cubex\Form\OptionBuilder;
use Sidekick\Components\Diffuse\Enums\ActionType;

class DiffuseActionForm extends Form
{
  public $actionType;
  public $comment;
  public $userRole;

  protected function _configure()
  {
    $this->_attribute("comment")->setType(FormElement::TEXTAREA)
    ->addValidator(Validator::VALIDATE_LENGTH, [10]);

    $this->_attribute("userRole")->setType(FormElement::SELECT);

    $this->_attribute("actionType")->setType(FormElement::SELECT)
    ->setOptions((new OptionBuilder(new ActionType()))->getOptions());
  }
}
