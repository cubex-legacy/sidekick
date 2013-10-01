<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Configuration;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Form\Form;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use
Sidekick\Applications\Diffuse\Views\Projects\Configuration\ApprovalConfigurationView;
use Sidekick\Applications\Diffuse\Views\Projects\ProjectNav;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Sidekick\Enums\Consistency;

class ApprovalController extends DiffuseController
{
  public function renderIndex()
  {
    $project   = new Project($this->getInt("projectId"));
    $platforms = Platform::orderedCollection();
    $approvals = ApprovalConfiguration::collection(
      ['project_id' => $project->id()]
    );
    $users     = ProjectUser::collection(['project_id' => $project->id()]);
    return new RenderGroup(
      $this->createView(new ProjectNav($this->request()->path(3), $project)),
      $this->createView(
        new ApprovalConfigurationView($project, $approvals, $platforms, $users)
      )
    );
  }

  public function postIndex()
  {
    if($this->request()->isForm() && Form::csrfCheck())
    {
      foreach($this->postVariables() as $platRole => $consistency)
      {
        list($platform, $role) = explode('-', $platRole, 2);

        $id = [
          $platform,
          $this->getInt("projectId"),
          $role
        ];

        $approval                   = new ApprovalConfiguration($id);
        $approval->consistencyLevel = Consistency::fromValue($consistency);
        $approval->required         = true;
        if($approval->consistencyLevel == Consistency::NONE)
        {
          $approval->delete();
        }
        else
        {
          $approval->saveChanges();
        }
      }
    }
    $msg = new TransportMessage("success", "Approval configuration update");
    return Redirect::to($this->request()->path(3))->with("msg", $msg);
  }
}
