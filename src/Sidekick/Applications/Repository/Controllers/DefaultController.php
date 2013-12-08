<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\Form\FormElement;
use Sidekick\Applications\Repository\Views\RepositoryIndex;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Repository;

class DefaultController extends RepositoryController
{
  public function renderIndex()
  {
    $repo = Repository::loadWhere(["project_id" => $this->getProjectId()]);
    if($repo !== null)
    {
      $branches = Branch::collection(
        ["repository_id" => $repo->id()]
      );
      return $this->createView(new RepositoryIndex($branches));
    }
    else
    {
      return $this->renderCreate();
    }
  }

  public function renderCreate()
  {
    $form = $this->_makeRepoForm(
      new Repository(),
    $this->appBaseUri() . '/create'
    );
    return $form;
  }

  public function postCreate()
  {
    $repo = Repository::loadWhereOrNew(["project_id" => $this->getProjectId()]);
    $repo->hydrate($this->request()->postVariables());
    $repo->saveChanges();

    $branch               = Branch::loadWhereOrNew(
      ["repository_id" => $repo->id()]
    );
    $branch->repositoryId = $repo->id();
    $branch->branch       = 'master';
    $branch->name         = 'master branch';
    $branch->saveChanges();

    $msg = new TransportMessage("success", "Repository Created");

    Redirect::to($this->appBaseUri())->with("msg", $msg)->now();
  }

  protected function _makeRepoForm(Repository $repo, $action)
  {
    $form            = new Form("Repository", $action);
    $repo->projectId = $this->getProjectId();
    $form->bindMapper($repo, true);
    $form->get("project_id")->setType(FormElement::HIDDEN);
    $form->get("username")->addAttribute("autocomplete", "off");
    $form->get("password")->addAttribute("autocomplete", "off");
    return $form;
  }

  public function postUpdate()
  {
    $repo = new Repository($this->request()->postVariables("id"));
    if($repo->exists())
    {
      $repo->hydrate($this->request()->postVariables());
      $repo->saveChanges();
      $msg = new TransportMessage("success", "Repository Updated");
    }
    else
    {
      $msg = new TransportMessage("error", "Repository Not Found");
    }
    Redirect::to($this->appBaseUri())->with("msg", $msg)->now();
  }

  public function renderUpdate()
  {
    $form = $this->_makeRepoForm(
      Repository::loadWhere(["project_id" => $this->getProjectId()]),
      ($this->appBaseUri() . '/update')
    );
    return $form;
  }

  public function getRoutes()
  {
    return [
      'update' => 'update',
      'create' => 'create'
    ];
  }
}
