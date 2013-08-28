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
use Cubex\Form\OptionBuilder;
use Cubex\Foundation\Container;
use Cubex\View\TemplatedViewModel;
use Nette\Utils\FileSystem;
use Sidekick\Applications\Diffuse\Forms\DeploymentStageForm;
use Sidekick\Components\Diffuse\Helpers\DeploymentHelper;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Deployment\BaseDeploymentService;

class ManageDeploymentStages extends TemplatedViewModel
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

  public function getStageName($stageId)
  {
    $stage = new DeploymentStage($stageId);
    if($stage->exists())
    {
      return $stage->name;
    }

    return "Unknown";
  }

  public function form()
  {
    $form = new DeploymentStageForm();
    $form->hydrateFromMapper($this->_stage);

    $buttonText = (!$this->_stage->exists()) ?
      "Create" : "Update";

    $form->addSubmitElement($buttonText);
    $form->getElement('submit')->addAttribute('class', 'btn');

    return $form;
  }
}
