<?php
/**
* @author: oke.ugwu
* Application:
*/
namespace Sidekick\Applications\Configurator\Views;

use Cubex\View\TemplatedViewModel;

class EnvironmentList extends TemplatedViewModel
{
  public function __construct($envs)
  {
    $this->environments = $envs;
  }
}
