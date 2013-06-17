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
    $this->_postCreateOrUpdateProject();
  }

  public function renderEditProject()
  {
    $projectId = $this->getInt('projectId');
    return new ProjectsForm($projectId);
  }

  public function postUpdateProject()
  {
    $this->_postCreateOrUpdateProject();
  }

  private function _postCreateOrUpdateProject()
  {
    $postData = $this->request()->postVariables();

    /**
     * Because we use bindMapper method (see Projects/views/ProjectForm)
     * to build this form.
     * There will always be an id field that defaults to an empty string.
     * Casting this id to an int can tell us if we are dealing with a create
     * or update request
     */
    if($postData['name'] != '')
    {
      if((int)$postData['id'])
      {
        $project   = new Project($postData['id']);
        $msg       = new \stdClass();
        $msg->type = 'success';
        $msg->text = 'Project was successfully updated';
      }
      else
      {
        $project   = new Project();
        $msg       = new \stdClass();
        $msg->type = 'success';
        $msg->text = 'Project was successfully created';
      }

      $project->name        = $postData['name'];
      $project->description = $postData['description'];
      $project->parentId    = $postData['parent_id'];
      $project->saveChanges();

      Redirect::to($this->baseUri())->with('msg', $msg)->now();
    }
    else
    {
      if((int)$postData['id'])
      {
        $redirectTo = $this->baseUri() . '/edit/' . $postData['id'];
        $error      = 'Project could be not updated. ' .
          'Please provide at least a name';
      }
      else
      {
        $redirectTo = $this->baseUri() . '/create-project';
        $error      = 'Project could be not created. ' .
          'Please provide at least a name';
      }

      $msg       = new \stdClass();
      $msg->type = 'error';
      $msg->text = $error;
      Redirect::to($redirectTo)->with('msg', $msg)->now();
    }
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
