<?php
/**
 * Author: oke.ugwu
 * Date: 28/08/13 18:24
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Configuration;

use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Cubex\View\TemplatedViewModel;
use Qubes\Bootstrap\Label;
use Sidekick\Components\Diffuse\Mappers\HostPlatform;

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
    foreach($this->platforms() as $platform)
    {
      foreach($this->hosts() as $host)
      {
        $hp = new HostPlatform(
          [
          $platform->id(),
          $this->_project->id(),
          $host->id
          ]
        );

        $this->_form->addCheckboxElement(
          "deploymentHosts[$platform->id][$host->id]",
          ($hp->exists()) ? true : false,
          true,
          FORM::LABEL_BEFORE
        );

        $this->_form->getElement(
          "deploymentHosts[$platform->id][$host->id]"
        )->setLabel($host->name);
      }
    }
    $this->_form->addSubmitElement('Save');

    return $this->_form;
  }

  public function getHosts($platformId)
  {
    $hosts = HostPlatform::collection(
      ['project_id' => $this->_project->id(), 'platform_id' => $platformId]
    );

    return $hosts;
  }
}
