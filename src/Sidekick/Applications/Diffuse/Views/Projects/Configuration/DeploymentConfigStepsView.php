<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Configuration;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\DeploymentStep;

class DeploymentConfigStepsView extends TemplatedViewModel
{
  protected $_platform;
  protected $_stages;

  public function __construct($platform, $stages)
  {
    $this->_platform = $platform;
    $this->_stages    = $stages;
  }

  public function stages()
  {
    return $this->_stages;
  }

  public function platform()
  {
    return $this->_platform;
  }

  public function className($str)
  {
    $parts = explode('\\', $str);
    return end($parts);
  }

  public function deploymentSteps($platformId)
  {
    return DeploymentStep::collection(
      [
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
