<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 26/07/13
 * Time: 10:03
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Configuration;

use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Cubex\Foundation\Container;
use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Diffuse\Forms\DeploymentStageForm;
use Sidekick\Components\Diffuse\Helpers\DeploymentHelper;
use Sidekick\Components\Projects\Mappers\Project;

class ManageDeploymentStepsView extends TemplatedViewModel
{
  protected $_stage;

  public function __construct($stage = null)
  {
    $this->_stage = $stage;
    $this->requireJs("stageConfig");
  }

  public function stage()
  {
    return $this->_stage;
  }

  public function form()
  {
    $form = new DeploymentStageForm();
    $form->hydrateFromMapper($this->_stage);

    $buttonText = 'Create';
    if($this->_stage->exists())
    {
      $buttonText = 'Update';
      $form->getElement('platformId')->setType(FormElement::HIDDEN);
    }

    $form->addSubmitElement($buttonText);
    $form->getElement('submit')->addAttribute('class', 'btn');

    return $form;
  }
}
