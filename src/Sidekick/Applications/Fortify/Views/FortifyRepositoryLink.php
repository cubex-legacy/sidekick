<?php
/**
 * Author: oke.ugwu
 * Date: 13/06/13 16:43
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\Form\Form;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Projects\Mappers\Project;

class FortifyRepositoryLink extends TemplatedViewModel
{
  protected $_form;
  protected $_project;
  protected $_build;
  protected $_repo;
  protected $_repoOptions;

  public function __construct($project, $repo, $build, $repoOptions)
  {
    $this->_project     = $project;
    $this->_repo        = $repo;
    $this->_build       = $build;
    $this->_repoOptions = $repoOptions;
  }

  protected function _form()
  {
    if($this->_form === null)
    {
      $this->_form = new Form(
        'buildProjectRepo', $this->appBaseUri() . '/repository/create'
      );
      $this->_form->addSelectElement(
        'repository',
        $this->_repoOptions,
        $this->_repo->id()
      );
      $this->_form->addHiddenElement('projectId', $this->_project->id());
      $this->_form->addHiddenElement('buildId', $this->_build->id());
      $this->_form->addSubmitElement('Update', 'submit');
      $this->_form->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }

    return $this->_form;
  }
}
