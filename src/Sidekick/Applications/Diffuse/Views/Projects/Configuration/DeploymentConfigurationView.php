<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Configuration;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;

class DeploymentConfigurationView extends TemplatedViewModel
{
  protected $_project;
  protected $_platforms;
  protected $_stages;

  public function __construct($project, $platforms, $stages)
  {
    $this->_project   = $project;
    $this->_platforms = $platforms;
    $this->_stages    = $stages;
  }

  public function project()
  {
    return $this->_project;
  }

  public function stages()
  {
    return $this->_stages;
  }

  public function platforms()
  {
    return $this->_platforms;
  }

  public function className($str)
  {
    $parts = explode('\\', $str);
    return end($parts);
  }

  public function platformStages($platformId)
  {
    return DeploymentStage::collection(
      [
      'project_id'  => $this->_project->id(),
      'platform_id' => $platformId
      ]
    )->setOrderBy('order');
  }

  public function getDetails($details)
  {
    if($details == null)
    {
      return '';
    }
    $render = new RenderGroup();
    foreach($details as $key => $value)
    {
      $render->add(new HtmlElement("p", [], "$key: $value"));
    }
    return $render;
  }
}
