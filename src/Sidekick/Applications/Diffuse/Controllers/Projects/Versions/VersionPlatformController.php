<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Versions;

use Cubex\Core\Http\Redirect;
use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Session;
use Cubex\Form\Form;
use Cubex\Helpers\Strings;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Queue\StdQueue;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Forms\DiffuseActionForm;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionPlatformView;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Helpers\VersionApproval;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\Platform;
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

    $platformState = new PlatformVersionState(
      [$platformId, $this->_version->id()]
    );

    $reqPlatforms = $platform->requiredPlatforms;
    foreach($this->_platformStates as $state)
    {
      if($state->state === VersionState::APPROVED)
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
      $this->verifyPlatform($users, $actions, $approvals, $platformState);
    }

    $platformView = new VersionPlatformView(
      $platform, $actions, $deployments, $approvals, $platformState
    );
    $platformView->setProjectUsers($users);

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
  protected function verifyPlatform(
    $projectUsers, $actions, $approvalRules, PlatformVersionState $platformState
  )
  {
    $requires  = $optional = [];
    $rejected  = false;
    $approvers = 0;
    foreach(VersionApproval::status(
              $projectUsers,
              $actions,
              $approvalRules
            ) as $state)
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
      $platformState->state = VersionState::REJECTED;
    }
    else if((!(in_array(false, $requires) ||
    (empty($requires) && in_array(false, $optional)))
    && $approvers > 0)
    )
    {
      //Any requirements fail, or any optionals require if no required pass
      $platformState->state = VersionState::APPROVED;
    }
    else if($approvers === 0)
    {
      $platformState->state = VersionState::PENDING;
    }
    else
    {
      $platformState->state = VersionState::REVIEW;
    }

    $platformState->saveChanges();

    $states   = $this->_platformStates->getKeyPair("platformId", "state");
    $complete = array_fill_keys(
      $this->_platforms->loadedIds(),
      VersionState::APPROVED
    );
    $diff     = array_diff_assoc($complete, $states);
    if(empty($diff))
    {
      $this->_version->versionState = VersionState::APPROVED;
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
          if($this->_version->versionState !== VersionState::APPROVED)
          {
            $this->_version->versionState = VersionState::REJECTED;
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
        [VersionState::PENDING, VersionState::REVIEW, VersionState::UNKNOWN]
      )
      )
      {
        $this->_form->getElement("actionType")->setOptions(
          ["comment" => "Comment"]
        );
      }

      //Show users own roles on form
      $roles = new ProjectUser([
                               $this->_version->projectId,
                               \Auth::user()->getId()
                               ]);
      $this->_form->getElement("userRole")->setOptions(
        array_fuse($roles->roles)
      );
    }
    return $this->_form;
  }

  public function deploy()
  {
    $deployRequest             = new \stdClass;
    $deployRequest->platformId = $this->getInt("platformId");
    $deployRequest->versionId  = $this->getInt("versionId");
    \Queue::push(new StdQueue('DeployRequest'), $deployRequest);

    //Change pending versions to review when first deployment made
    if($this->_version->versionState === VersionState::PENDING)
    {
      $this->_version->versionState = VersionState::REVIEW;
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
