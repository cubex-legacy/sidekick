<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 30/05/13
 * Time: 12:42
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Projects\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Projects\Forms\ProjectForm;

class CreateProject extends TemplatedViewModel
{
  protected $_form;
  protected $_title;

  public function __construct($title, ProjectForm $form)
  {
    $this->_title = $title;
    $this->_form = $form;
  }

  public function form()
  {
    return $this->_form;
  }

  public function title()
  {
    return $this->_title;
  }
}
