<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;
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

class DefaultController extends BaseControl
{
  public function renderIndex()
  {
    $ver              = new Version(1);
    $ver->major       = 1;
    $ver->releaseDate = time();
    $ver->saveChanges();

    $repo                       = new Repository(1);
    $repo->projectId            = 1;
    $repo->versionControlSystem = 'git';
    $repo->path                 = 'git://jdi';
    $repo->pushType             = PushType::DOUBLE_AUTH_OR_MANAGER;
    $repo->saveChanges();

    $review               = new VersionReview(1);
    $review->reviewStatus = VersionState::PASSED;
    $review->approverId   = 1;
    $review->message      = 'All looks good';
    $review->saveChanges();

    $release               = new Release(1);
    $release->repositoryId = 1;
    $release->liveVersion  = '1.1.2';
    $release->stageVersion = '1.1.3';
    $release->saveChanges();

    $user              = new User(1);
    $user->displayName = 'Brooke';
    $user->saveChanges();

    $repoUser           = new RespositoriesUsers($repo, $user);
    $repoUser->userRole = UserRole::MANAGER;
    $repoUser->saveChanges();

    $commit = new Commit(1);
    $commit->author = 'gareth';
    $commit->message = "Some Update";
    $commit->commitHash = '0a4521';
    $commit->repositoryId = 1;
    $commit->saveChanges();

    $proj       = new Project(1);
    $proj->name = "JDI Backup : Wilma";
    $proj->saveChanges();

    echo "Code Distributions";
  }
}
