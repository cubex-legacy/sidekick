<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Versions;

use Cubex\Core\Http\Redirect;
use Cubex\Data\Transportable\TransportMessage;
use Cubex\Form\Form;
use Cubex\Helpers\Strings;
use Cubex\Queue\StdQueue;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Forms\DiffuseActionForm;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionPlatformView;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\Platform;
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

    $platformView = new VersionPlatformView(
      $platform, $actions, $deployments, $approvals
    );
    $platformView->setProjectUsers($users);

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

    $platformView->setActionForm($this->_buildForm());

    return $this->_buildView(
      $platformView
    );
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
      $action->saveChanges();
      //TODO: If reject, handle closing version
      //TODO: Comment and Reject actions should always have a comment
    }
    return (new Redirect())->to($this->baseUri());
  }

  protected function _buildForm()
  {
    if($this->_form === null)
    {
      $this->_form = new DiffuseActionForm("diffuseAction");
      $roles       = new ProjectUser([
                                     $this->_version->projectId,
                                     \Auth::user()->getId()
                                     ]);
      $this->_form->getElement("userRole")->setOptions(
        array_fuse($roles->roles)
      );
    }
    return $this->_form;
  }

  public function processAction()
  {
  }

  public function deploy()
  {
    $deployRequest             = new \stdClass;
    $deployRequest->platformId = $this->getInt("platformId");
    $deployRequest->versionId  = $this->getInt("versionId");
    \Queue::push(new StdQueue('DeployRequest'), $deployRequest);

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
