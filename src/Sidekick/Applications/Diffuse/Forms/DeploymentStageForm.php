<?php
/**
 * Author: oke.ugwu
 * Date: 27/08/13 17:48
 */

namespace Sidekick\Applications\Diffuse\Forms;

use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Cubex\Form\OptionBuilder;
use Sidekick\Components\Diffuse\Helpers\DeploymentHelper;
use Sidekick\Components\Diffuse\Mappers\DeploymentConfig;
use Sidekick\Components\Projects\Mappers\Project;

class DeploymentStageForm extends Form
{
  public $id;
  public $name;
  public $platformId;
  public $command;
  protected $_mapper;

  public function __construct($action = '')
  {
    parent::__construct('StageEdit', $action);
  }

  protected function _configure()
  {
    $this->setDefaultElementTemplate("<label>{{label}}</label>{{input}}");
    $this->_attribute("id")->setType(FormElement::HIDDEN);

    $this->addSelectElement(
      "platformId",
      (new OptionBuilder(DeploymentConfig::collection()))->getOptions()
    );

    $this->_attribute("platformId")->setLabel("Platform");

    $this->addTextElement('name');
    $this->addTextareaElement('command');

  }
}
