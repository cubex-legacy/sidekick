<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Versions;

use Cubex\Form\Form;
use Cubex\View\TemplatedViewModel;

class VersionChangeLogView extends TemplatedViewModel
{
  /**
   * @var Form
   */
  protected $_form;

  public function setForm(Form $form)
  {
    $this->_form = $form;
    $this->_form->getElement("changelog")
    ->addAttribute("class", "span12")
    ->addAttribute("rows", "10");
    return $this;
  }

  public function getForm()
  {
    return $this->_form;
  }
}
