<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 25/07/13
 * Time: 15:52
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views\Project;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;

class DeploymentStages extends TemplatedViewModel
{
  /**
   * @var \Sidekick\Components\Diffuse\Mappers\DeploymentStage[]
   */
  protected $_stages;
  protected $_projectId;

  public function __construct($projectId,$stages)
  {
    $this->_projectId = $projectId;
    $this->_stages = $stages;
  }

  public function getDeploymentStages()
  {
    return $this->_stages;
  }

  public function getDetails($details)
  {
    if($details == null)
    {
      return new HTMLElement("p", [], "");
    }
    $render = new RenderGroup();
    foreach($details as $key => $value)
    {
      $render->add(new HtmlElement("p", [], "$key: $value"));
    }
    return $render;
  }
}
