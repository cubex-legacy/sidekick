<?php
/**
 * Author: oke.ugwu
 * Date: 28/08/13 18:24
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\TemplatedViewModel;

class DeploymentStagesView extends TemplatedViewModel
{
  protected $_deploymentlist; 
  protected $_deployment;

  public function __construct($deploymentlist, $deployment)
  {
    $this->_deploymentlist = $deploymentlist; 
    $this->_deployment = $deployment;
  }

  /**
   * @return mixed
   */
  public function getDeployment()
  {
    return $this->_deployment;
  }   

  /**
   * @return null
   */
  public function getDeploymentlist()
  {
    return $this->_deploymentlist;
  }
  public function textClass($result)
  {
    $return = "text-info";
    if($result == 'fail')
    {
      $return = "text-error";
    }
    elseif($result == 'pass')
    {
      $return = "text-success";
    }
    return $return;
  }

}
