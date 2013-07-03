<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Helpers;

use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Mappers\Version;

class VersionHelper
{
  public static function nextVersion(
    $projectId, $majorIncr = 0, $minorIncr = 0, $buildIncr = 1,
    $revisionIncr = 0
  )
  {
    $batchSize = 10;
    $processed = 0;

    $versions = Version::collection()
                ->whereEq("project_id", $projectId)
                ->whereNeq("version_state", VersionState::REJECTED)
                ->setOrderBy('id', 'DESC')
                ->setLimit($processed, $batchSize)->get();

    while($versions->hasMappers())
    {
      $exceptionStatus = false;
      //Loop through all versions, if 10 found,
      // and no complete build found, loop next 10
      foreach($versions as $version)
      {
        if($majorIncr > 0)
        {
          $version->minor = $version->build = $version->revision = 0;
        }

        if($minorIncr > 0)
        {
          $version->build = $version->revision = 0;
        }

        if($buildIncr > 0)
        {
          $version->revision = 0;
        }
        /**
         * @var $version Version
         */
        //Do not increment ontop of failed builds, only successful releases
        switch(VersionState::fromValue($version->versionState))
        {
          case VersionState::APPROVED:
            return [
              $version->major + $majorIncr,
              $version->minor + $minorIncr,
              $version->build + $buildIncr,
              $version->revision + $revisionIncr
            ];
          case VersionState::REJECTED:

            break;
          case VersionState::PENDING:
            $exceptionStatus = 'pending';
            break;
          case VersionState::REVIEW:
            $exceptionStatus = 'in review';
            break;
          case VersionState::UNKNOWN:
            $exceptionStatus = 'in limbo';
            break;
        }

        if($exceptionStatus)
        {
          //Throw exception if a version is already in review for this project
          throw new \Exception(
            "A new version of this project cannot be created as one is " .
            $exceptionStatus
          );
        }
      }
      if($versions->count() == $batchSize)
      {
        $processed += $batchSize;
        $versions->setLimit($processed, $batchSize)->get();
      }
      else
      {
        $versions->clear();
        break;
      }
    }

    //Handle first version
    return [$majorIncr, $minorIncr, $buildIncr, $revisionIncr];
  }
}
