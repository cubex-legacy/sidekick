<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Versions;

use Cubex\Core\Http\Redirect;
use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Session;
use Cubex\Form\Form;
use Cubex\Mapper\Database\RecordCollection;
use Sidekick\Applications\Diffuse\Forms\DiffuseActionForm;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionPlatformView;
use Sidekick\Components\Enums\ActionType;
use Sidekick\Components\Enums\ApprovalState;
use Sidekick\Components\Diffuse\Helpers\VersionApproval;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformProjectConfig;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Projects\Mappers\ProjectUser;

class VersionPlatformController extends VersionsController
{
  protected $_form;

  public function renderIndex()
  {
    $platformId = $this->getInt("platformId");
    $platform   = new Platform($platformId);

    $actions = Action::collection(
      ['version_id' => $this->_version->id(), 'platform_id' => $platformId]
    )->setOrderBy("created_at", "DESC");

    $deployments = Deployment::collection(
      ['version_id' => $this->_version->id(), 'platform_id' => $platformId]
    )->setLimit(0, 5)
      ->setOrderBy("id", "DESC");

    $approvals = ApprovalConfiguration::collection(
      [
        'platform_id' => $platformId,
        'project_id'  => $this->_version->projectId
      ]
    );

    $users = ProjectUser::collection(
      ['project_id' => $this->_version->projectId]
    );

    $platConfig = new PlatformProjectConfig(
      [$platformId, $this->_version->projectId]
    );

    $platformState = new PlatformVersionState(
      [$platformId, $this->_version->id()]
    );

    $reqPlatforms = $platform->requiredPlatforms;
    foreach($this->_platformStates as $state)
    {
      if($state->state === ApprovalState::APPROVED)
      {
        $key = array_search($state->platformId, $platform->requiredPlatforms);
        if($key !== false)
        {
          unset($reqPlatforms[$key]);
        }
      }
    }

    if(empty($reqPlatforms))
    {
      $this->_verifyPlatform($users, $actions, $approvals, $platformState);
    }

    $platformView = new VersionPlatformView(
      $platform, $actions, $deployments, $approvals, $platformState, $platConfig
    );
    $platformView->setProjectUsers($users);
    $platformView->setVersion($this->_version);

    if($reqPlatforms)
    {
      $platformView->setRequiredPlatforms(
        array_map(
          '\Cubex\Helpers\Strings::titleize',
          Platform::collection()->loadIds($reqPlatforms)
            ->getUniqueField("name")
        )
      );
    }

    $platformView->setActionForm($this->_buildForm($platformState));

    //used in deploymentDetailsView to return back to $platformView
    Session::set('backUri', $this->baseUri());

    return $this->_buildView(
      $platformView
    );
  }

  /**
   * @param $projectUsers  RecordCollection|ProjectUser[]
   * @param $actions       RecordCollection|Action[]
   * @param $approvalRules RecordCollection|ApprovalConfiguration[]
   * @param $platformState PlatformVersionState
   *
   * @return array
   */
  protected function _verifyPlatform(
    $projectUsers, $actions, $approvalRules, PlatformVersionState $platformState
  )
  {
    $requires  = $optional = [];
    $rejected  = false;
    $approvers = 0;

    $states = VersionApproval::status($projectUsers, $actions, $approvalRules);
    foreach($states as $state)
    {
      if($state['require_pass'])
      {
        $requires[] = $state['pending'] == 0;
      }
      else
      {
        $optional[] = $state['pending'] == 0;
      }

      $approvers += count($state['approvers']);

      if(!empty($state['rejectors']))
      {
        $rejected = true;
      }
    }

    if($rejected)
    {
      $platformState->state = ApprovalState::REJECTED;
    }
    else if((!(in_array(false, $requires) ||
        (empty($requires) && in_array(false, $optional)))
      && $platformState->deploymentCount > 0)
    )
    {
      //Any requirements fail, or any optionals require if no required pass
      $platformState->state = ApprovalState::APPROVED;
    }
    else if($approvers === 0)
    {
      $platformState->state = ApprovalState::PENDING;
    }
    else
    {
      $platformState->state = ApprovalState::REVIEW;
    }

    $platformState->saveChanges();

    $states = $this->_platformStates->getKeyPair("platformId", "state");

    $states[$platformState->id()] = $platformState->state;
    $complete                     = array_fill_keys(
      $this->_platforms->loadedIds(),
      ApprovalState::APPROVED
    );
    $diff                         = array_diff_assoc($complete, $states);
    if(empty($diff))
    {
      $this->_version->versionState = ApprovalState::APPROVED;
      $this->_version->saveChanges();
    }
  }

  public function postIndex()
  {
    if($this->_request->isForm() && Form::csrfCheck())
    {
      $action = new Action();
      $action->hydrate(
        $this->postVariables(["actionType", "comment", "userRole"])
      );
      $action->versionId  = $this->getInt("versionId");
      $action->userId     = \Auth::user()->getId();
      $action->platformId = $this->getInt("platformId");

      if($action->actionType !== ActionType::APPROVE)
      {
        $form = $this->_buildForm();
        $form->hydrateFromMapper($action);
        if(!$form->isValid("comment"))
        {
          return $this->renderIndex();
        }
      }
      $action->saveChanges();

      switch($action->actionType)
      {
        case ActionType::REJECT;
          //If rejected, close version
          if($this->_version->versionState !== ApprovalState::APPROVED)
          {
            $this->_version->versionState = ApprovalState::REJECTED;
            $this->_version->saveChanges();
          }
          else
          {
            throw new \Exception(
              "Why are you trying to reject an approved version?"
            );
          }
          break;
      }
    }
    return (new Redirect())->to($this->baseUri());
  }

  protected function _buildForm(PlatformVersionState $platformState = null)
  {
    if($this->_form === null)
    {
      $this->_form = new DiffuseActionForm("diffuseAction");

      //Only allow comments on non "complete" versions
      if(!in_array(
        $platformState ? $platformState->state : $this->_version->versionState,
        [ApprovalState::PENDING, ApprovalState::REVIEW, ApprovalState::UNKNOWN]
      )
      )
      {
        $this->_form->getElement("actionType")->setOptions(
          ["comment" => "Comment"]
        );
      }

      //Show users own roles on form
      $roles = new ProjectUser(
        [
          $this->_version->projectId,
          \Auth::user()->getId()
        ]
      );
      $this->_form->getElement("userRole")->setOptions(
        array_fuse($roles->roles)
      );
    }
    return $this->_form;
  }

  public function deploy()
  {
    $deployment             = new Deployment();
    $deployment->pending    = true;
    $deployment->platformId = $this->getInt("platformId");
    $deployment->projectId  = $this->getProjectId();
    $deployment->versionId  = $this->getInt("versionId");
    $deployment->userId     = \Auth::user()->getId();
    $deployment->saveChanges();

    //Change pending versions to review when first deployment made
    if($this->_version->versionState === ApprovalState::PENDING)
    {
      $this->_version->versionState = ApprovalState::REVIEW;
      $this->_version->saveChanges();
    }

    \Session::flash(
      "msg",
      new TransportMessage(
        "success",
        "Deployment Queued",
        "Your deployment request has been queued, and will process shortly"
      )
    );
    return (new Redirect())->to($this->baseUri());
  }

  public function getRoutes()
  {
    return [
      '/deploy' => 'deploy'
    ];
  }
}
