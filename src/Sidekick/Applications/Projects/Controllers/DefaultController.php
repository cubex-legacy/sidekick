<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Projects\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Sidekick\Applications\Projects\Views\ProjectsForm;
use Sidekick\Applications\Projects\Views\ProjectsIndex;
use Sidekick\Applications\Projects\Views\ProjectsSidebar;
use Sidekick\Components\Projects\Mappers\Project;

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
    return new ProjectsForm();
  }

  public function postCreateProject()
  {
    $postData             = $this->request()->postVariables();
    $project              = new Project();
    $project->name        = $postData['name'];
    $project->description = $postData['description'];
    if((int)$postData['parent'] != 0)
    {
      $project->parentId = $postData['parent'];
    }

    $project->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Project was successfully created';

    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function renderEditProject()
  {
    $projectId = $this->getInt('projectId');
    return new ProjectsForm($projectId);
  }

  public function postUpdateProject()
  {
    $postData = $this->request()->postVariables();

    $project              = new Project($postData['id']);
    $project->name        = $postData['name'];
    $project->description = $postData['description'];
    $project->parentId    = $postData['parent_id'];
    $project->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Project was successfully updated';

    Redirect::to($this->baseUri())->with('msg', $msg)->now();
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

  public function getRoutes()
  {
    return array(
      '/create-project'    => 'createProject',
      '/update-project'    => 'updateProject',
      '/view/:projectId'   => 'viewProject',
      '/delete/:projectId' => 'deleteProject',
      '/edit/:projectId'   => 'editProject',
    );
  }
}
