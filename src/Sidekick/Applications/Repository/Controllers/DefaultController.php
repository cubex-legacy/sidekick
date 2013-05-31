<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository\Controllers;

use Cubex\Facade\Redirect;
use Sidekick\Applications\Repository\Views\CommitsIndex;
use Sidekick\Applications\Repository\Views\RepositoryForm;
use Sidekick\Applications\Repository\Views\RepositoryIndex;
use Sidekick\Components\Repository\Mappers\Source;

class DefaultController extends RepositoryController
{
  public function renderIndex()
  {
    return $this->createView(new RepositoryIndex());
  }

  public function renderAddRepo()
  {
    return $this->createView(new RepositoryForm());
  }

  public function postAddRepo()
  {
    $postData = $this->request()->postVariables();

    $source = new Source();
    $source->hydrate($postData);
    $source->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Repository was successfully created';

    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }


  public function renderEditRepo()
  {
    $repoId = $this->getInt('repoId');
    return $this->createView(new RepositoryForm($repoId));
  }

  public function postUpdateRepo()
  {
    $postData = $this->request()->postVariables();

    $source = new Source($postData['id']);
    $source->hydrate($postData);
    $source->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Project was successfully updated';

    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function renderDeleteRepo()
  {
    $repoId = $this->getInt('repoId');
    $source = new Source($repoId);
    $source->delete();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Repository was deleted successfully';

    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function renderRepoCommits()
  {
    $repoId = $this->getInt('repoId');
    return $this->createView(new CommitsIndex($repoId));
  }

  public function renderCommitSrc()
  {
    return 'Source Code to be displayed here. Maybe a diff';
  }

  public function getRoutes()
  {
    return [
      'add-repository'    => 'addRepo',
      'update-repository' => 'updateRepo',
      'edit/:repoId'      => 'editRepo',
      'delete/:repoId'    => 'deleteRepo',
      'commits/:repoId'   => 'repoCommits',
      'src/:commitId'     => 'commitSrc'
    ];
  }
}
