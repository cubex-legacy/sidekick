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
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Projects\Mappers\Project;

class DeploymentStageForm extends Form
{
  public $id;
  public $name;
  public $platformId;
  public $serviceClass;
  public $requireAllHostsPass;
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
      (new OptionBuilder(Platform::orderedCollection()))->getOptions()
    );

    $this->_attribute("platformId")->setLabel("Platform");

    $serviceClassOptions = DeploymentHelper::getServiceClassOptions();
    $serviceClassOptions = ["" => '-SELECT-'] + $serviceClassOptions;

    $this->addSelectElement("serviceClass", $serviceClassOptions);
    $this->addSelectElement("requireAllHostsPass", ["No", "Yes"]);
  }
}
