<?php
/**
 * Author: oke.ugwu
 * Date: 28/08/13 18:24
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\TemplatedViewModel;

class DeploymentHostsView extends TemplatedViewModel
{
  protected $_project;
  protected $_hosts;
  protected $_platforms;
  protected $_form;

  public function __construct($project, $hosts, $platforms)
  {
    $this->_project   = $project;
    $this->_hosts     = $hosts;
    $this->_platforms = $platforms;
  }

  public function hosts()
  {
    return $this->_hosts;
  }

  public function platforms()
  {
    return $this->_platforms;
  }

  public function form()
  {
    if($this->_form != null)
    {
      return $this->_form;
    }
    $this->_form = new Form('deploymentHosts');
    $this->_form->setDefaultElementTemplate('{{input}}');

    $this->_form->addSelectElement(
      "platformId",
      (new OptionBuilder($this->_platforms))->getOptions()
    );

    foreach($this->hosts() as $host)
    {
      $this->_form->addCheckboxElement(
        "deploymentHosts[$host->id]",
        false,
        true,
        FORM::LABEL_BEFORE
      );

      $this->_form->getElement(
        "deploymentHosts[$host->id]"
      )->setLabel($host->name);
    }

    $this->_form->addSubmitElement('Deploy');

    return $this->_form;
  }

  public function project()
  {
    return $this->_project;
  }
}
