<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 30/05/13
 * Time: 14:56
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Projects\Views;

use Cubex\Form\Form;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Components\Projects\Mappers\Project;

class ProjectsForm extends ViewModel
{
  /*
   * @var $_type Form Type
   * What type of form you want to render. create or update
   * */
  protected $_type = 'create';
  protected $_projectId;

  public function __construct($projectId = null)
  {
    $this->_projectId = $projectId;
    if($this->_projectId !== null)
    {
      $this->_type = 'update';
    }
  }

  public function form()
  {
    $formTitle = ucwords($this->_type . ' Project');

    $form = new Form('addProject', '/projects/' . $this->_type . '-project');
    $form->addAttribute('class', 'well');
    $form->bindMapper(new Project($this->_projectId));

    return new RenderGroup("<h1>$formTitle</h1>", $form);
  }

  public function render()
  {
    return $this->form();
  }
}
