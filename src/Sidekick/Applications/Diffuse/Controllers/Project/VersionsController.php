<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Project;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\Project\VersionsList;
use Sidekick\Applications\Diffuse\Views\Project\VersionDetails;
use Sidekick\Applications\Diffuse\Views\Project\VersionHistory;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Commit;

class VersionsController extends DiffuseProjectController
{
  public function renderIndex()
  {
    $projectId = $this->getInt('projectId');

    $versions = Version::collection(['project_id' => $projectId])
    ->load()
    ->setOrderBy("updated_at", "DESC");
    return new VersionsList($versions, $projectId);
  }

  public function postCreate()
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

  public function renderDetails()
  {
    $projectId = $this->getInt('projectId');
    $versionId = $this->getInt('versionId');
    return new VersionDetails($projectId, $versionId, $this->getNav());
  }

  public function renderChangeLog()
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

  public function renderHistory()
  {
    return new VersionHistory($this->getInt("versionId"), $this->getNav(
      "history"
    ));
  }

  public function getRoutes()
  {
    return [
      '/'          => 'details',
      '/changelog' => 'changeLog',
    ];
  }
}
