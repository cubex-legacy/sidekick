<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 25/07/13
 * Time: 15:52
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;

class DeploymentStages extends TemplatedViewModel
{

  public function __construct()
  {
  }

  public function getDeploymentStages()
  {
    return DeploymentStage::collection()->loadAll();
  }

  public function getDetails($details)
  {
    $render = new RenderGroup();
    foreach($details as $key => $value)
    {
      $render->add(new HtmlElement("p", [], "$key: $value"));
    }
    return $render;
  }
}
