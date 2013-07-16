<?php
/**
 * Author: oke.ugwu
 * Date: 02/07/13 13:21
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Facade\Auth;
use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Enums\VersionType;
use Sidekick\Components\Projects\Mappers\ProjectUser;

class VersionDetails extends TemplatedViewModel
{
  /**
   * @var $_version \Sidekick\Components\Diffuse\Mappers\Version
   */
  protected $_version;
  /**
   * @var $_version \Sidekick\Components\Diffuse\Mappers\Platform[]
   */
  protected $_platforms;
  protected $_actions;
  protected $_actionForm;
  protected $_deployForm;
  protected $_deployments;
  protected $_projectUsers;
  public $autoApprove;

  public function __construct(
    $version, $actions, $platforms, $deployments, $projectUsers, $autoApprove
  )
  {
    $this->_version      = $version;
    $this->_actions      = $actions;
    $this->_platforms    = $platforms;
    $this->_deployments  = $deployments;
    $this->_projectUsers = $projectUsers;
    $this->autoApprove   = $autoApprove;
  }

  public function getVersion()
  {
    return $this->_version;
  }

  public function getVersionTypeName()
  {
    return VersionType::constFromValue($this->_version->type);
  }

  public function getActions()
  {
    return $this->_actions;
  }

  public function getPlatforms()
  {
    return $this->_platforms;
  }

  public function getDeployments()
  {
    return $this->_deployments;
  }

  public function getProjectMembers()
  {
    return $this->_projectUsers;
  }

  public function actionForm()
  {
    if($this->_actionForm === null)
    {
      $this->_actionForm = new Form(
        'actionForm',
        '/diffuse/' . $this->_version->projectId . '/' . $this->_version->id(
        ) . '/comment'
      );
      $this->_actionForm->setDefaultElementTemplate('{{input}}');
      $this->_actionForm->addSelectElement(
        'actionType',
        (new OptionBuilder(new ActionType))->getOptions()
      );
      $this->_actionForm->addTextareaElement('comment');
      $this->_actionForm->addHiddenElement('versionId', $this->_version->id());
      $this->_actionForm->addHiddenElement('userId', Auth::user()->getId());
      $this->_actionForm->addSubmitElement('Submit');

      //add custom attributes
      $this->_actionForm->getElement('comment')->addAttribute(
        'style',
        'width:98%'
      )->setRequired(true);
      $this->_actionForm->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }

    return $this->_actionForm;
  }

  public function deployForm()
  {
    if($this->_deployForm === null)
    {
      $this->_deployForm = new Form(
        'actionForm',
        '/diffuse/' . $this->_version->projectId . '/' . $this->_version->id(
        ) . '/deploy'
      );
      $this->_deployForm->setDefaultElementTemplate('{{input}}');
      $this->_deployForm->addSelectElement(
        'platformId',
        $this->_platforms->getKeyPair('id', 'name')
      );
      $this->_deployForm->addTextareaElement('comment');

      $this->_deployForm->addHiddenElement('userId', Auth::user()->getId());
      $this->_deployForm->addHiddenElement('versionId', $this->_version->id());
      $this->_deployForm->addSubmitElement('Deploy!');

      //add custom attributes
      $this->_deployForm->getElement('comment')->addAttribute(
        'style',
        'width:98%'
      )->addAttribute('placeholder', 'Add Comment Here')->setRequired(true);

      $this->_deployForm->getElement('submit')->addAttribute(
        'class',
        'btn btn-primary'
      );
    }

    return $this->_deployForm;
  }
}
