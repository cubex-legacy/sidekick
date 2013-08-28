<?php
/**
 * Author: oke.ugwu
 * Date: 27/08/13 16:19
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Configuration;

use Cubex\View\TemplatedViewModel;

class DeploymentConfigurationOptions extends TemplatedViewModel
{
  protected $_serviceClass;
  protected $_configuration;

  public function __construct($serviceClass, $configuration)
  {
    $this->_serviceClass  = $serviceClass;
    $this->_configuration = $configuration;
  }

  public function getConfigurationOptions()
  {
    $serviceProvider = $this->_serviceClass;
    if(class_exists($this->_serviceClass))
    {
      return $serviceProvider::getConfigurationItems();
    }

    return [];
  }

  public function configurationValue($key)
  {
    return isset($this->_configuration->$key) ?
      $this->_configuration->$key : '';
  }

  public function disabled($key)
  {
    return ($this->configurationValue($key) === '') ?
      'disabled="disabled"' : '';
  }

  public function checked($key)
  {
    return ($this->configurationValue($key) !== '') ?
      'checked' : '';
  }

}
