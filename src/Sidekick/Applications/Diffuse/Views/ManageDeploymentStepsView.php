<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 26/07/13
 * Time: 10:03
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Cubex\Foundation\Container;
use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Diffuse\Forms\DeploymentStageForm;
use Sidekick\Components\Diffuse\Helpers\DeploymentHelper;
use Sidekick\Components\Projects\Mappers\Project;

class ManageDeploymentStepsView extends TemplatedViewModel
{
  protected $_step;
  public $configId;

  public function __construct($step = null)
  {
    $this->_step = $step;
    $this->requireJs("stageConfig");
  }

  public function step()
  {
    return $this->_step;
  }

  public function form()
  {
    $form = new DeploymentStageForm();
    $form->hydrateFromMapper($this->_step);
    $form->platformId = $this->configId;

    $buttonText = 'Create';
    if($this->_step->exists())
    {
      $buttonText = 'Update';
      $form->getElement('platformId')->setType(FormElement::HIDDEN);
    }

    $form->addSubmitElement($buttonText);
    $form->getElement('submit')->addAttribute('class', 'btn');

    return $form;
  }
}
