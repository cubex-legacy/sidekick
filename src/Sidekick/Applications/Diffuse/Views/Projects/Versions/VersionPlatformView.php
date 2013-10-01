<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Versions;

use Cubex\Helpers\Strings;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\HtmlElement;
use Cubex\View\Impart;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Qubes\Bootstrap\Icon;
use Sidekick\Applications\Diffuse\Forms\DiffuseActionForm;
use Sidekick\Components\Diffuse\Helpers\VersionApproval;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformProjectConfig;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Users\Mappers\User;

class VersionPlatformView extends TemplatedViewModel
{
  protected $_platformId;
  protected $_platform;
  /**
   * @var Action[]|RecordCollection
   */
  protected $_actions;
  /**
   * @var RecordCollection|ApprovalConfiguration[]
   */
  protected $_approvals;
  /**
   * @var RecordCollection|ProjectUser[]
   */
  protected $_projectUsers;
  /**
   * @var Platform[]|RecordCollection
   */
  protected $_requiredPlatforms;
  /**
   * @var DiffuseActionForm
   */
  protected $_form;
  /**
   * @var PlatformProjectConfig
   */
  protected $_platformConfig;

  public function __construct(
    Platform $platform, $actions, $deployments, $approvals,
    PlatformVersionState $platformState, PlatformProjectConfig $platformConfig
  )
  {
    $this->_platformId     = $platform->id();
    $this->_platform       = $platform;
    $this->_platformState  = $platformState;
    $this->_actions        = $actions;
    $this->_deployments    = $deployments;
    $this->_approvals      = $approvals;
    $this->_platformConfig = $platformConfig;
  }

  public function setProjectUsers($users)
  {
    $this->_projectUsers = $users;
    return $this;
  }

  /**
   * @return Action[]
   */
  public function getActions()
  {
    return $this->_actions;
  }

  /**
   * @return Deployment[]
   */
  public function getDeployments()
  {
    return $this->_deployments;
  }

  /**
   * @return ApprovalConfiguration[]
   */
  public function getApprovalConfigs()
  {
    return $this->_approvals;
  }

  public function getApprovalBoxes()
  {
    $boxes = new RenderGroup();

    foreach(VersionApproval::status(
              $this->_projectUsers,
              $this->_actions,
              $this->_approvals
            ) as $state)
    {
      $message = '';

      if($state['pending'] < 1)
      {
        $status = new Icon(Icon::ICON_OK);
        $class  = 'alert-success';
      }
      else if($state['pending'] && count($state['approvers']) > 0)
      {
        $status = 'Waiting for ' . $this->tp("%d other(s)", $state['pending']);
        $class  = 'alert-success';
      }
      else
      {
        $message = $this->tp("%d required", $state['required']);
        $status  = 'Awaiting Approval';
        $class   = 'alert-error';
      }

      if(!$state['require_pass'])
      {
        $class = 'alert-info';
      }

      $approvalBox = new HtmlElement('div', ['class' => 'alert ' . $class]);
      $approvalBox->nestElement(
        'strong',
        [],
        (Strings::titleize($state['role']) . " Approval: ")
      );
      $approvalBox->nest(
        new Impart($message ? : implode_list(
          User::collection()->loadIds($state['approvers'])
          ->getUniqueField("displayName")
        ))
      );
      $approvalBox->nestElement("span", ['class' => 'pull-right'], $status);
      $boxes->add($approvalBox);
    }

    return $boxes;
  }

  public function setActionForm(DiffuseActionForm $form)
  {
    $form->getElement("comment")
    ->addAttribute("class", "span12")
    ->addAttribute("rows", 3);

    $this->_form = $form;
    return $this;
  }

  public function getActionForm()
  {
    return $this->_form;
  }

  public function requiredPlatforms()
  {
    return $this->_requiredPlatforms;
  }

  public function setRequiredPlatforms($requiredPlatforms)
  {
    $this->_requiredPlatforms = $requiredPlatforms;
    return $this;
  }

  public function getPlatformConfig()
  {
    return $this->_platformConfig;
  }
}
