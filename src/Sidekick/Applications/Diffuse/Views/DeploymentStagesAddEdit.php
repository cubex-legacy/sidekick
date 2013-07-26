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
use Cubex\Form\OptionBuilder;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Projects\Mappers\Project;

class DeploymentStagesAddEdit extends TemplatedViewModel
{

  protected $_stage;

  public function __construct($stage = null)
  {
    $this->_stage = ($stage == null) ? new DeploymentStage() : $stage;
    $this->requireJsLibrary("jquery");
    $this->requireJs("stageConfig");
  }

  public function getForm()
  {
    $form = new Form("StageEdit");
    $form->addHiddenElement("id", $this->_stage->id());
    $form->addSelectElement(
      "project",
      (new OptionBuilder(Project::getProjects()))->getOptions(),
      $this->_stage->projectId
    );
    $form->addSelectElement(
      "platform",
      (new OptionBuilder(Platform::collection()))->getOptions(),
      $this->_stage->platformId
    );
    $form->addTextElement("serviceClass", $this->_stage->serviceClass);
    $form->addSelectElement(
      "requireAllHostsPass",
      ["No", "Yes"],
      $this->_stage->requireAllHostsPass
    );

    $form->addSubmitElement(
      ($this->_stage->id(
        ) == null) ? "Create Deployment Stage" : "Update Deployment Stage"
    );
    return $form;
  }
}
