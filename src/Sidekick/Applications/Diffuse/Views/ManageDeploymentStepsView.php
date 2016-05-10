<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 26/07/13
 * Time: 10:03
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Form\FormElement;
use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Diffuse\Forms\DeploymentStageForm;
use Sidekick\Components\Diffuse\Mappers\DeploymentStep;

class ManageDeploymentStepsView extends TemplatedViewModel
{
  /**
   * @var DeploymentStep $_step
   */
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
 
    if($this->_step->exists())
    {
      $form->platformId = $this->_step->platformId;
      $form->getElement('platformId')->setType(FormElement::HIDDEN);
      $form->addSubmitElement('Update');
      $form->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }
    else
    {
      $form->addSubmitElement('Create');
      $form->getElement('submit')->addAttribute(
        'class',
        'btn btn-success'
      );
    }

    $form->getElement('submit')->addAttribute('class', 'btn btn-primary');

    return $form;
  }
}
