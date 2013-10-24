<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Projects\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Projects\Forms\ProjectForm;
use Sidekick\Applications\Projects\Views\ProjectsIndex;
use Sidekick\Applications\Projects\Views\ProjectsSidebar;
use Sidekick\Applications\Projects\Views\ProjectUsersList;
use Sidekick\Components\Users\Enums\UserRole;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Users\Mappers\User;

class DefaultController extends ProjectsController
{
  public function preRender()
  {
    parent::preRender();
    $this->requireCss('base');
    $this->nest('sidebar', new ProjectsSidebar());
  }

  public function renderIndex()
  {
    $projects = Project::collection()->loadAll()
                ->setOrderBy('name')->preFetch('parent');
    return $this->createView(new ProjectsIndex($projects));
  }

  public function renderCreateProject()
  {
    return new RenderGroup(
      new HtmlElement('h1', [], 'Create Project'),
      new ProjectForm('/projects/create-project')
    );
  }

  public function postCreateProject()
  {
    $postData = $this->request()->postVariables();
    $form     = new ProjectForm('/projects/create-project');
    $form->hydrate($postData);
    if($form->isValid())
    {
      //save project
      $project              = new Project();
      $project->name        = $form->name;
      $project->description = $form->description;
      if($form->parent_id > 0)
      {
        $project->parentId = $form->parent_id;
      }
      $project->saveChanges();

      $msg       = new \stdClass();
      $msg->type = 'success';
      $msg->text = 'Project was successfully created';

      Redirect::to($this->baseUri())->with('msg', $msg)->now();
    }
    else
    {
      $redirectTo = $this->baseUri() . '/create-project';
      $error      = 'Project could be not created. ' .
        'Please provide at least a name';

      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = $error;
      Redirect::to($redirectTo)->with('msg', $msg)->now();
    }

    return new RenderGroup(
      new HtmlElement('h1', [], 'Create Project'),
      $form
    );
  }

  public function renderEditProject()
  {
    $projectId = $this->getInt('projectId');
    $form      = new ProjectForm('/projects/update-project', $projectId);

    return new RenderGroup(
      new HtmlElement('h1', [], 'Update Project'),
      $form
    );
  }

  public function postUpdateProject()
  {
    $postData = $this->request()->postVariables();
    $form     = new ProjectForm('/projects/update-project');
    $form->hydrate($postData);
    if($form->isValid())
    {
      //save project
      $project              = new Project($postData['id']);
      $project->name        = $form->name;
      $project->description = $form->description;
      if($form->parent_id > 0)
      {
        $project->parentId = $form->parent_id;
      }
      $project->saveChanges();

      $msg       = new \stdClass();
      $msg->type = 'success';
      $msg->text = 'Project was successfully updated';

      Redirect::to($this->baseUri())->with('msg', $msg)->now();
    }
    else
    {
      $redirectTo = $this->baseUri() . '/update-project';
      $error      = 'Project could be not updated. ' .
        'Please provide at least a name';

      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = $error;
      Redirect::to($redirectTo)->with('msg', $msg)->now();
    }

    return $form;
  }

  public function renderDeleteProject()
  {
    $projectId = $this->getInt('projectId');
    $project   = new Project($projectId);
    $project->delete();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Project was deleted successfully';

    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function renderUsers()
  {
    $projectId = $this->getInt('projectId');

    $users = User::collection()->loadAll()->getKeyPair('id', 'display_name');

    $form = new Form('projectUsers', '/projects/users/' . $projectId);
    $form->addSelectElement('userId', $users);
    $form->addHiddenElement('projectId', $projectId);
    $form->addCheckboxElements(
      'roles',
      null,
      (new OptionBuilder(new UserRole))->getOptions()
    );
    $form->addSubmitElement('Add User');

    $postData = $this->request()->postVariables();
    if($postData)
    {
      $projectUser            = new ProjectUser();
      $projectUser->userId    = $postData['userId'];
      $projectUser->projectId = $postData['projectId'];
      $projectUser->roles     = $postData['roles'];
      $projectUser->saveChanges();
    }

    $project      = new Project($projectId);
    $projectUsers = ProjectUser::collection(['project_id' => $projectId])
                    ->load();
    $list         = $this->createView(new ProjectUsersList($projectUsers));
    return new RenderGroup(
      '<h1>' . $project->name . ': Project Members</h1>',
      $form,
      $list
    );
  }

  public function renderRemoveUser()
  {
    $projectId = $this->getInt('projectId');
    $userId    = $this->getInt('userId');

    $projectUser = new ProjectUser([$projectId, $userId]);
    $projectUser->delete();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Project member was removed successfully';

    Redirect::to('/projects/users/' . $projectId)->with(
      'msg',
      $msg
    )->now();
  }

  public function getRoutes()
  {
    return array(
      '/create-project'                 => 'createProject',
      '/update-project'                 => 'updateProject',
      '/view/:projectId'                => 'viewProject',
      '/delete/:projectId'              => 'deleteProject',
      '/edit/:projectId'                => 'editProject',
      '/users/:projectId'               => 'users',
      '/remove-user/:projectId/:userId' => 'removeUser'
    );
  }
}
