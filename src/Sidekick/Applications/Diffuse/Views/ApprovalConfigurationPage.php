<?php
/**
 * Author: oke.ugwu
 * Date: 02/07/13 17:16
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\Diffuse\Forms\ApprovalConfigurationForm;
use Sidekick\Components\Diffuse\Mappers\ApprovalConfiguration;
use Sidekick\Components\Diffuse\Mappers\Platform;

class ApprovalConfigurationPage extends TemplatedViewModel
{
  public $projectId;

  public function __construct($projectId)
  {
    $this->projectId = $projectId;
  }

  public function getPlatforms()
  {
    $platforms = Platform::collection()->loadAll();
    return ($platforms !== null) ? $platforms : [];
  }

  public function getConfigurations($platformID)
  {
    $configs = ApprovalConfiguration::collection()->loadWhere(
      ["project_id" => $this->projectId, "platform_id" => $platformID]
    );
    return ($configs !== null) ? $configs : [];
  }
}
