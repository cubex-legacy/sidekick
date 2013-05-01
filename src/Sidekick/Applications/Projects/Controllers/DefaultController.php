<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Applications\Projects\Controllers;

use Cubex\Form\Form;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Components\Projects\Mappers\Project;

class DefaultController extends BaseControl
{

  public function renderIndex()
  {
    echo "<h1>Add Projects</h1>";

    $form = new Form('addProject', '/projects/add-project');
    $form->addTextElement('name', '');

    $projects    = Project::collection()->loadAll()->getKeyPair('id', 'name');
    $projects[0] = 'None';

    $form->addSelectElement('parent', $projects);
    $form->addTextareaElement('description', '');
    $form->addSubmitElement('Add', 'submit');
    $form->addAttribute("class", "well");
    $form->addAttribute("style", "width:400px;");
    echo $form;
  }

  public function postAddProject()
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

    echo "Project Added!";
  }

  public function getRoutes()
  {
    return array(
      '/projects/add-project' => 'addProject',
    );
  }
}