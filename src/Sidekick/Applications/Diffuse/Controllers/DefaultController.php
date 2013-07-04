<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\VersionDetails;
use Sidekick\Applications\Diffuse\Views\VersionsList;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Helpers\ApprovalConfigurationHelper;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Repository\Mappers\Commit;

class DefaultController extends DiffuseController
{
  public function renderIndex()
  {
    $projectId = $this->getInt('projectId');
    echo "<h1>Code Distributions</h1>";
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
    $version   = new Version($versionId);
    $actions   = Action::collection(['version_id' => $versionId]);
    $platforms = Platform::collection();

    /**
     * If Version has been approved, show ALL platforms
     * If Version has not been approved, show only platforms that do not
     * require approval for deployment
     */
    if($version->versionState == VersionState::APPROVED)
    {
      $platforms = $platforms->loadAll();
    }
    if($version->versionState != VersionState::APPROVED)
    {
      $platforms = $platforms->loadWhere(['require_approval' => false]);
    }

    $deployments = Deployment::collection(['version_id' => $versionId])
                   ->setOrderBy('created_at', 'DESC');

    $projectUsers = ProjectUser::collection(
      ['project_id' => $projectId]
    );

    $autoApprove = ApprovalConfigurationHelper::isAutoApproveReady(
      $actions,
      $projectId
    );

    return new VersionDetails(
      $version, $actions, $platforms, $deployments, $projectUsers, $autoApprove
    );
  }

  public function renderAddComment()
  {
    $projectId = $this->getInt('projectId');
    $versionId = $this->getInt('versionId');
    $postData  = $this->request()->postVariables();

    $action = new Action();
    $action->hydrate($postData);
    $action->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Comment added';
    Redirect::to($this->baseUri() . '/' . $projectId . '/' . $versionId)
    ->with('msg', $msg)->now();
  }

  public function renderDeploy()
  {
    $projectId = $this->getInt('projectId');
    $versionId = $this->getInt('versionId');

    $deployment = new Deployment();
    $deployment->hydrate($this->request()->postVariables());
    $deployment->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Version deployed successfully';
    Redirect::to($this->baseUri() . '/' . $projectId . '/' . $versionId)
    ->with('msg', $msg)->now();
  }

  public function getRoutes()
  {
    return [
      '/create-version'                    => 'createVersion',
      '/:projectId'                        => 'versions',
      '/:projectId/:versionId@num'         => 'versionDetails',
      '/:projectId/:versionId@num/comment' => 'addComment',
      '/:projectId/:versionId@num/deploy'  => 'deploy',
    ];
  }
}
