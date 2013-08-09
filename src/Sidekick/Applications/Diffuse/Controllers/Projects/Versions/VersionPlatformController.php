<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Versions;

use Cubex\Helpers\Strings;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionPlatformView;
use Sidekick\Components\Diffuse\Mappers\Action;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Projects\Mappers\ProjectUser;

class VersionPlatformController extends VersionsController
{
  public function renderIndex()
  {
    $platformId = $this->getInt("platformId");
    $platform   = new Platform($platformId);

    $actions = Action::collection(
      ['version_id' => $this->_version->id(), 'platform_id' => $platformId]
    )->setOrderBy("created_at", "DESC");

    $deployments = Deployment::collection(
      ['version_id' => $this->_version->id(), 'platform_id' => $platformId]
    );

    $approvals = ApprovalConfiguration::collection(
      [
      'platform_id' => $platformId,
      'project_id'  => $this->_version->projectId
      ]
    );

    $users = ProjectUser::collection(
      ['project_id' => $this->_version->projectId]
    );

    $platformView = new VersionPlatformView(
      $platform, $actions, $deployments, $approvals
    );
    $platformView->setProjectUsers($users);

    if($platform->requiredPlatforms)
    {
      $platformView->setRequiredPlatforms(
        array_map(
          '\Cubex\Helpers\Strings::titleize',
          Platform::collection()->loadIds($platform->requiredPlatforms)
          ->getUniqueField("name")
        )
      );
    }

    return $this->_buildView(
      $platformView
    );
  }
}
