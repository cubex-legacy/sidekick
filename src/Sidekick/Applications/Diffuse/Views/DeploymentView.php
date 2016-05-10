<?php
/**
 * Author: oke.ugwu
 * Date: 28/08/13 18:24
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Mappers\Deployment;

class DeploymentView extends TemplatedViewModel
{
  protected $_project;
  protected $_hosts;
  protected $_platforms;
  protected $_form;
  protected $_buildRun;

  public function __construct($project, $hosts, $platforms, $buildRun)
  {
    $this->_project = $project;
    $this->_hosts = $hosts;
    $this->_platforms = $platforms;
    $this->_buildRun = $buildRun;
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
    $this->_form->addHiddenElement('buildId', $this->_buildRun->id());

    $options = (new OptionBuilder($this->_platforms))->getOptions();
    $options = [0 => 'Select a Config'] + $options;

    $this->_form->addSelectElement("platformId", $options);
    $this->_form->addTextElement('deploy_base', $this->_project->deployBase);

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
      )->setLabel($host->hostname);
    }

    $this->_form->addTextAreaElement('comment');
    $this->_form->addSubmitElement('Deploy');

    //get previous deployment for this project and pre-populate form
    $lastDeployment = Deployment::collection()->loadWhere(
      ['project_id' => $this->_project->id()]
    )->setOrderBy('id', 'DESC')->first();

    if($lastDeployment)
    {
      $this->_form->getElement('platformId')->setData(
        $lastDeployment->platformId
      );
      if($lastDeployment->deployBase)
      {
        $this->_form->getElement('deploy_base')->setData(
          $lastDeployment->deployBase
        );
      }

      $lastDeployHosts = json_decode($lastDeployment->hosts);
      if(is_array($lastDeployHosts))
      {
        foreach($this->hosts() as $host)
        {
          if(in_array($host->id, $lastDeployHosts))
          {
            $this->_form->getElement("deploymentHosts[$host->id]")->setDat(
              true
            );
          }
        }
      }
    }

    return $this->_form;
  }

  public function project()
  {
    return $this->_project;
  }
}
