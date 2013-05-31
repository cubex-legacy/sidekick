<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 30/05/13
 * Time: 14:56
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Repository\Views;

use Cubex\Form\Form;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Components\Repository\Mappers\Source;

class RepositoryForm extends ViewModel
{
  /*
   * @var $_type Form Type
   * What type of form you want to render. add or update
   * */
  protected $_type = 'add';
  protected $_repoId;

  public function __construct($repoId = null)
  {
    $this->_repoId = $repoId;
    if($this->_repoId !== null)
    {
      $this->_type = 'update';
    }
  }

  public function form()
  {
    $formTitle = ucwords($this->_type . ' Repository');

    $form = new Form(
      'addProject',
      $this->baseUri() . '/' . $this->_type . '-repository'
    );
    $form->addAttribute('class', 'well');
    $form->bindMapper(new Source($this->_repoId));

    return new RenderGroup("<h1>$formTitle</h1>", $form);
  }

  public function render()
  {
    return $this->form();
  }
}
