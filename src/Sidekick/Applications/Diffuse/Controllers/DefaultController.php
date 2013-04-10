<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Cubex\Form\Form;
use Sidekick\Components\Diffuse\Mappers\Commit;
use Sidekick\Components\Diffuse\Mappers\PushType;
use Sidekick\Components\Diffuse\Mappers\Release;
use Sidekick\Components\Diffuse\Mappers\Repository;
use Sidekick\Components\Diffuse\Mappers\RespositoriesUsers;
use Sidekick\Components\Diffuse\Mappers\UserRole;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Diffuse\Mappers\VersionReview;
use Sidekick\Components\Diffuse\Mappers\VersionState;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Users\Mappers\User;

class DefaultController extends DiffuseController
{
  public function renderIndex()
  {
    echo "Code Distributions";
  }
}
