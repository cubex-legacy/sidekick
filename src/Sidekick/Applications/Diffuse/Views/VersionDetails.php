<?php
/**
 * Author: oke.ugwu
 * Date: 02/07/13 13:21
 */

namespace Sidekick\Applications\Diffuse\Views;

use Cubex\Facade\Auth;
use Cubex\Form\Form;
use Cubex\Form\OptionBuilder;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Diffuse\Enums\ActionType;
use Sidekick\Components\Diffuse\Enums\VersionType;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Projects\Mappers\ProjectUser;
use Sidekick\Components\Users\Enums\UserRole;

class VersionDetails extends TemplatedViewModel
{
  protected $_nav;
  protected $_projectID;
  /**
   * @var $_version \Sidekick\Components\Diffuse\Mappers\Version
   */
  protected $_versionID;

  public function __construct($projectID, $versionID, $nav)
  {
    $this->_projectID = $projectID;
    $this->_versionID = $versionID;
    $this->_nav       = $nav;
  }

  public function getVersion()
  {
    $version = Version::collection()->loadOneWhere(["id" => $this->_versionID]);
    return $version;
  }

  public function getUsers()
  {
    $projectUsers = ProjectUser::collection(
      ['project_id' => $this->_projectID]
    );
    return $projectUsers;
  }

  public function getPlatforms()
  {
    $platforms = Platform::collection()->loadAll();
    return $platforms;
  }

  public function getStateOnPlatform($platformID)
  {
    $pvs = PlatformVersionState::collection()->loadOneWhere(
      [
      "platform_id" => $platformID,
      "version_id"  => $this->_versionID
      ]
    );
    return ($pvs == null) ? "" : $pvs->state;
  }

  public function getNav()
  {
    return $this->_nav;
  }
}
