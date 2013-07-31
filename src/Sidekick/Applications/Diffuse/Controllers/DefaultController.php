<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Core\Http\Response;
use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\DeploymentStages;
use Sidekick\Applications\Diffuse\Views\DeploymentStagesAddEdit;
Use Sidekick\Applications\Diffuse\Views\HomePage;
use Sidekick\Applications\Diffuse\Views\VersionDetails;
use Sidekick\Applications\Diffuse\Views\VersionHistory;
use Sidekick\Applications\Diffuse\Views\VersionsList;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\DeploymentStage;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Repository\Mappers\Commit;

class DefaultController extends DiffuseController
{
  public function renderIndex()
  {
    $state=$this->_request->getVariables("stateSelect");
    $platform=$this->_request->getVariables("allPlatforms");
    return $this->createView(new HomePage($state, ($platform != null)? true : false));
  }

  /*
   * Creates a stdclass for each version, with members corresponding to the version details
   * States are a nested stdclass (see getStatesForVersion)
   *
   * Returns a json response (from the stdclass) with the matching versions
   */
  public function ajaxIndex()
  {
    $requestedState=$this->_request->postVariables("state");
    $allPlatforms=($this->_request->postVariables("allplatforms")=="true") ? true : false;
    $versions=Version::collection()->loadAll();
    $json=new \StdClass;
    foreach($versions as $version)
    {

      $versionObj=new \StdClass;
      $project=new Project($version->projectId);
      $versionObj->projectid=$version->projectId;
      $versionObj->project=$project->name;
      $versionObj->version=$version->major . "." . $version->minor . "." . $version->build;
      $versionObj->type=$version->type;
      $versionObj->states=$this->getStatesForVersion($version->id, $requestedState, $allPlatforms);
      if($versionObj->states==null)
      {
        continue;
      }
      $versionObj->updated=date("d/M/Y",strtotime($version->updatedAt));
      $json->{$version->id}=$versionObj;
    }
    return new Response($json);
  }

  /*
   * Creates a stdclass with the states as members, e.g.
   * "dev" => "approved"
   * "stage" => "review"
   *
   * Returns null if none of the states match $requestedState
   * Returns null if $allPlatforms is true and not all of the states equal $requestedState
   * Returns the stdclass otherwise
   */
  public function getStatesForVersion($versionId, $requestedState, $allPlatforms)
  {
    $states=PlatformVersionState::collection()->loadWhere(["version_id"=>$versionId]);
    $stateObj=new \StdClass;
    $hasRequestedState=false;
    $hasAllRequestedStates=true;
    foreach($states as $state)
    {
      if($state->state==$requestedState)
      {
        $hasRequestedState=true;
      }
      else
      {
        $hasAllRequestedStates=false;
      }
      $platform=new Platform($state->platformId);
      $stateObj->{$platform->name}=$state->state;
    }
    if(!$hasRequestedState || ($allPlatforms && !$hasAllRequestedStates))
    {
      return null;
    }
    return $stateObj;
  }

  public function renderVersions()
  {
    $projectId = $this->getInt('projectId');

    $versions = Version::collection(['project_id' => $projectId])->load();
    return new VersionsList($versions, $projectId);
  }

  public function postCreateVersion()
  {
    $type              = $this->request()->postVariables('type');
    $versionNumberType = $this->request()->postVariables('versionNumberType');
    $projectId         = $this->request()->postVariables('projectId');

    try
    {
      $versionArr = VersionHelper::getVersionArr(
        $versionNumberType,
        $projectId
      );
      $project    = new Project($projectId);

      //create version
      $version               = new Version();
      $version->major        = $versionArr[0];
      $version->minor        = $versionArr[1];
      $version->build        = $versionArr[2];
      $version->revision     = $versionArr[3];
      $version->projectId    = $projectId;
      $version->type         = $type;
      $version->versionState = VersionState::PENDING;
      //this will return repo id for master branch
      if($project->repository())
      {
        $version->repoId = $project->repository()->id();

        //Get latest passing build
        $latestPassingBuild = BuildRun::collection(
          ['project_id' => $projectId, 'result' => 'pass']
        );
        $latestPassingBuild = $latestPassingBuild->setOrderBy(
          'start_time',
          'DESC'
        );
        $latestPassingBuild = $latestPassingBuild->setLimit(0, 1)->first();
        if($latestPassingBuild)
        {
          $version->toCommitHash = $latestPassingBuild->commitHash;
        }

        //Get oldest build
        $oldestBuild = BuildRun::collection(
          ['project_id' => $projectId, 'result' => 'pass']
        );
        $oldestBuild = $oldestBuild->setOrderBy(
          'start_time',
          'ASC'
        );
        $oldestBuild = $oldestBuild->setLimit(0, 1)->first();
        if($oldestBuild)
        {
          $version->fromCommitHash = $oldestBuild->commitHash;
        }

        //Get Change log
        //Basically this is all the comment messages from the commit range
        //only attempt this if we have a valid commit range
        if($oldestBuild && $latestPassingBuild)
        {
          $startCommit = Commit::collection()->loadOneWhere(
            ['commit_hash' => $oldestBuild->commitHash]
          );

          $endCommit = Commit::collection()->loadOneWhere(
            ['commit_hash' => $latestPassingBuild->commitHash]
          );

          $commits = Commit::collection()->loadWhere(
            '%C BETWEEN %d AND %d AND %C = %d',
            'id',
            $startCommit->id(),
            $endCommit->id(),
            'repository_id',
            $version->repoId
          );

          $changeLog = '';
          foreach($commits as $c)
          {
            $changeLog .= '- ' . $c->subject;
            if($c->message != '')
            {
              $changeLog .= ':' . $c->message . PHP_EOL;
            }
            else
            {
              $changeLog .= PHP_EOL;
            }
          }

          $version->changeLog = $changeLog;
        }

        $version->buildId = $latestPassingBuild->buildId;
      }

      $version->saveChanges();

      $msg       = new \stdClass();
      $msg->type = 'success';
      $msg->text = ucfirst($type) . ' Version created successfully';
      Redirect::to($this->baseUri() . '/' . $projectId)->with(
        'msg',
        $msg
      )->now();
    }
    catch(\Exception $e)
    {
      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = $e->getMessage();

      Redirect::to($this->baseUri() . '/' . $projectId)->with(
        'msg',
        $msg
      )->now();
    }
  }

  public function renderVersionDetails()
  {
    $projectId = $this->getInt('projectId');
    $versionId = $this->getInt('versionId');
    return new VersionDetails($projectId, $versionId, $this->getNav());
  }

  public function renderDeploy()
  {
    $projectId  = $this->getInt('projectId');
    $versionId  = $this->getInt('versionId');
    $platformId = $this->getInt('platform');
    $platform   = Platform::collection()->loadOneWhere(["id" => $platformId]);
    //Is it allowed?
    foreach($platform->requiredPlatforms as $required)
    {
      $pvs = PlatformVersionState::collection()->loadOneWhere(
        ["platform_id" => $required, "version_id" => $versionId]
      );
      if($pvs == null || $pvs->state != VersionState::APPROVED)
      {
        $msg       = new \stdClass();
        $msg->type = 'error';
        $msg->text = 'Version is not approved on a required previous platform';
        Redirect::to(
          $this->baseUri() . '/' . $projectId . '/' . $versionId
        )
        ->with('msg', $msg)->now();
      }
    }
    $deployment = new Deployment();
    $deployment->hydrate(
      [
      "version_id"  => $versionId,
      "platform_id" => $platformId,
      "user_id"     => \Auth::user()->getId(),
      "project_id"  => $projectId,
      "deployed_on" => date("Y-m-d"),
      "comment"     => ""
      ]
    );
    $deployment->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Version deployed successfully';
    Redirect::to(
      $this->baseUri(
      ) . '/platform/' . $projectId . '/' . $versionId . '/' . $platformId
    )
    ->with('msg', $msg)->now();
  }

  public function renderVersionChangeLog()
  {
    $render = new RenderGroup();
    $render->add("<h1>Change Log</h1>");
    $render->add($this->getNav("changelog"));
    $version = Version::collection()->loadOneWhere(
      ["id" => $this->getInt("versionId")]
    );
    $form    = new Form("changelog");
    $form->addHiddenElement("version_id", $this->getInt("versionId"));
    $form->addTextAreaElement("change_log", $version->changeLog);
    $form->addSubmitElement("Update Change Log");
    $form->getElement("change_log")->addAttribute(
      "style",
      "width:100%; height:100px;"
    );
    $render->add($form);
    return $render;
  }

  public function renderVersionHistory()
  {
    return new VersionHistory($this->getInt("versionId"), $this->getNav(
      "history"
    ));
  }

  public function renderStages()
  {
    return new DeploymentStages();
  }

  public function renderStageAdd()
  {
    return new DeploymentStagesAddEdit();
  }

  public function renderStageEdit()
  {
    $stageID = $this->getInt("stageId");
    $stage   = DeploymentStage::collection()->loadOneWhere(["id" => $stageID]);
    return new DeploymentStagesAddEdit($stage);
  }

  public function postStageAdd()
  {
    //Create configuration object
    $cKeys  = $this->_request->postVariables("configurationKeys");
    $cVals  = $this->_request->postVariables("configurationValues");
    $config = new \StdClass();
    for($i = 0; $i < count($cKeys); $i++)
    {
      $config->{$cKeys[$i]} = $cVals[$i];
    }
    //Create dependencies object
    $dKeys = $this->_request->postVariables("dependencyKeys");
    $dVals = $this->_request->postVariables("dependencyValues");
    $deps  = new \StdClass();
    for($i = 0; $i < count($dKeys); $i++)
    {
      $deps->{$dKeys[$i]} = $dVals[$i];
    }
    $stage = new DeploymentStage();
    $stage->hydrate(
      [
      "platform_id"            => $this->_request->postVariables("platform"),
      "project_id"             => $this->_request->postVariables("project"),
      "service_class"          => $this->_request->postVariables(
        "serviceClass"
      ),
      "require_all_hosts_pass" => $this->_request->postVariables(
        "requireAllHostsPass"
      ),
      "configuration"          => $config,
      "dependencies"           => $deps
      ]
    );
    $stage->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Deployment Stage created successfully';
    Redirect::to($this->baseUri() . "/stages")->with('msg', $msg)->now();
  }

  public function postStageEdit()
  {
    //Create configuration object
    $cKeys  = $this->_request->postVariables("configurationKeys");
    $cVals  = $this->_request->postVariables("configurationValues");
    $config = new \StdClass();
    for($i = 0; $i < count($cKeys); $i++)
    {
      $config->{$cKeys[$i]} = $cVals[$i];
    }
    //Create dependencies object
    $dKeys = $this->_request->postVariables("dependencyKeys");
    $dVals = $this->_request->postVariables("dependencyValues");
    $deps  = new \StdClass();
    for($i = 0; $i < count($dKeys); $i++)
    {
      $deps->{$dKeys[$i]} = $dVals[$i];
    }
    $stage = new DeploymentStage();
    $stage->hydrate(
      [
      "id"                     => $this->_request->postVariables("id"),
      "platform_id"            => $this->_request->postVariables("platform"),
      "project_id"             => $this->_request->postVariables("project"),
      "service_class"          => $this->_request->postVariables(
        "serviceClass"
      ),
      "require_all_hosts_pass" => $this->_request->postVariables(
        "requireAllHostsPass"
      ),
      "configuration"          => $config,
      "dependencies"           => $deps
      ]
    );
    $stage->saveChanges();
    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Deployment Stage updated successfully';
    Redirect::to($this->baseUri() . "/stages")->with('msg', $msg)->now();
  }

  public function renderStageDelete()
  {
    $stageID = $this->getInt("stageId");
    $stage   = DeploymentStage::collection()->loadOneWhere(["id" => $stageID]);
    $stage->delete();
    $msg       = new \stdClass();
    $msg->type = "success";
    $msg->text = "Stage deleted successfully";
    Redirect::to($this->baseUri() . "/stages")->with('msg', $msg)->now();
  }

  public function getNav($page = "")
  {
    $project = $this->getInt("projectId");
    $version = $this->getInt("versionId");
    $active  = ["class" => "active"];
    $list    = new HTMLElement("ul", ["class" => "nav nav-tabs"]);
    $list->nestElement(
      "li",
      ($page == "") ? $active : [],
      "<a href='/diffuse/$project/$version/'>Version Details</a>"
    );
    $list->nestElement(
      "li",
      ($page == "changelog") ? $active : [],
      "<a href='/diffuse/$project/$version/changelog'>Change Log</a>"
    );
    $platforms = Platform::collection()->loadAll();
    foreach($platforms as $platform)
    {
      $list->nestElement(
        "li",
        ($page == $platform->name) ? $active : [],
        "<a href='/diffuse/platform/$project/$version/" . $platform->id . "'>" . $platform->name . "</a>"
      );
    }
    return $list;
  }

  public function getRoutes()
  {
    return [
      '/create-version'                             => 'createVersion',
      '/stages'                                     => 'stages',
      '/stages/add'                                 => 'stageAdd',
      '/stages/:stageId/edit'                       => 'stageEdit',
      '/stages/:stageId/delete'                     => 'stageDelete',
      '/:projectId'                                 => 'versions',
      '/:projectId/:versionId@num'                  => 'versionDetails',
      '/:projectId/:versionId@num/changelog'        => 'versionChangeLog',
      '/:projectId/:versionId@num/:platform/deploy' => 'deploy'
    ];
  }
}
