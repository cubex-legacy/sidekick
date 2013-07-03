<?php
/**
 * Author: oke.ugwu
 * Date: 03/07/13 14:15
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Sidekick\Applications\Diffuse\Forms\ApprovalConfigurationForm;
use Sidekick\Applications\Diffuse\Views\ApprovalConfigurationPage;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;

class ApprovalController extends DiffuseController
{
  public function renderIndex()
  {
    $projectId = $this->getInt('projectId');
    if($this->request()->postVariables())
    {
      $ac = new ApprovalConfiguration();
      $ac->hydrate($this->request()->postVariables());
      $ac->saveChanges();
    }

    $config = ApprovalConfiguration::collection(['project_id' => $projectId]);
    $form   = new ApprovalConfigurationForm(
      $projectId, null, ''
    );
    return new ApprovalConfigurationPage($form, $config, $projectId);
  }

  public function renderEdit()
  {
    $projectId = $this->getInt('projectId');
    $role      = $this->getStr('role');

    $config = ApprovalConfiguration::collection(['project_id' => $projectId]);
    $form   = new ApprovalConfigurationForm(
      $projectId, $role, ''
    );
    return new ApprovalConfigurationPage($form, $config, $projectId, $role);
  }

  public function postEdit()
  {
    $projectId = $this->getInt('projectId');

    $ac = new ApprovalConfiguration();
    $ac->hydrate($this->request()->postVariables());
    $ac->saveChanges();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Config successfully updated';
    Redirect::to($this->baseUri() . '/' . $projectId)->with(
      'msg',
      $msg
    )->now();
  }

  public function renderDelete()
  {
    $projectId = $this->getInt('projectId');
    $role      = $this->getStr('role');

    $ac = new ApprovalConfiguration([$projectId, $role]);
    $ac->delete();

    $msg       = new \stdClass();
    $msg->type = 'success';
    $msg->text = 'Config successfully deleted';
    Redirect::to($this->baseUri() . '/' . $projectId)->with(
      'msg',
      $msg
    )->now();
  }

  public function getRoutes()
  {
    return [
      '/:projectId'              => 'index',
      '/:projectId/:role/edit'   => 'edit',
      '/:projectId/:role/delete' => 'delete',
    ];
  }
}
