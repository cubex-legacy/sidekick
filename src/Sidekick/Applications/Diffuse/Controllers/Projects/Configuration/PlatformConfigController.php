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
Sidekick\Applications\Diffuse\Views\Projects\Configuration\PlatformConfigurationView;
use Sidekick\Applications\Diffuse\Views\Projects\ProjectNav;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformProjectConfig;
use Sidekick\Components\Projects\Mappers\Project;

class PlatformConfigController extends DiffuseController
{
  public function renderIndex()
  {
    $project   = new Project($this->getInt("projectId"));
    $platforms = Platform::orderedCollection();
    $configs   = PlatformProjectConfig::collection(
      ['project_id' => $project->id()]
    );
    return new RenderGroup(
      $this->createView(new ProjectNav($this->request()->path(3), $project)),
      $this->createView(
        new PlatformConfigurationView($project, $configs, $platforms)
      )
    );
  }

  public function postIndex()
  {
    if($this->request()->isForm() && Form::csrfCheck())
    {
      $projectId = $this->getInt("projectId");
      $updates   = [];
      foreach($this->postVariables() as $platKey => $value)
      {
        list($platform, $key) = explode('-', $platKey, 2);
        $updates[$platform][$key] = $value;
      }
      foreach($updates as $platformId => $configs)
      {
        $platCfg = new PlatformProjectConfig(
          [$platformId, $projectId]
        );
        if($platCfg->platformId === null)
        {
          $platCfg->platformId = $platformId;
        }
        if($platCfg->projectId === null)
        {
          $platCfg->projectId = $projectId;
        }
        foreach($configs as $key => $value)
        {
          $platCfg->setData($key, $value);
        }
        $platCfg->saveChanges();
      }
    }
    $msg = new TransportMessage("success", "Platform configuration updated");
    return Redirect::to($this->request()->path(3))->with("msg", $msg);
  }
}
