<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Helpers;

use Cubex\Foundation\Config\Config;
use Cubex\Foundation\Container;
use Sidekick\Components\Enums\ApprovalState;
use Sidekick\Components\Diffuse\Enums\VersionType;
use Sidekick\Components\Diffuse\Mappers\Version;

class VersionHelper
{
  public static function latestVersions($projectId, $batchSize = 10)
  {
    return Version::collection()
           ->whereEq("project_id", $projectId)
           ->whereNeq("version_state", ApprovalState::REJECTED)
           ->setOrderBy('id', 'DESC')
           ->setLimit(0, $batchSize)->get();
  }

  public static function nextVersion(
    $projectId, $majorIncr = 0, $minorIncr = 0, $buildIncr = 1,
    $revisionIncr = 0
  )
  {
    $batchSize = 10;
    $processed = 0;

    $versions = self::latestVersions($projectId, $batchSize);

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
        switch(ApprovalState::fromValue($version->versionState))
        {
          case ApprovalState::APPROVED:
            return [
              $version->major + $majorIncr,
              $version->minor + $minorIncr,
              $version->build + $buildIncr,
              $version->revision + $revisionIncr
            ];
          case ApprovalState::REJECTED:

            break;
          case ApprovalState::PENDING:
            $exceptionStatus = 'pending';
            break;
          case ApprovalState::REVIEW:
            $exceptionStatus = 'in review';
            break;
          case ApprovalState::UNKNOWN:
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

  public static function sourceLocation(Version $version)
  {
    $config   = Container::config()->get('diffuse', new Config());
    $basePath = $config->getStr(
      "versions_path",
      (dirname(WEB_ROOT) . DS . 'diffuse' . DS . 'versions' . DS)
    );

    $versionPath = [
      'P' . $version->projectId,
      $version->major,
      $version->minor,
      $version->build,
      VersionType::STANDARD === (string)VersionType::fromValue($version->type) ?
      VersionType::STANDARD :
      $version->type . $version->revision
    ];

    return $basePath . implode(DS, $versionPath) . DS;
  }

  public static function getVersionArr($type, $projectId)
  {
    switch($type)
    {
      case 'major':
        $versionArr = VersionHelper::nextVersion($projectId, 1, 0, 0, 0);
        break;
      case 'minor':
        $versionArr = VersionHelper::nextVersion($projectId, 0, 1, 0, 0);
        break;
      case 'build':
        $versionArr = VersionHelper::nextVersion($projectId, 0, 0, 1, 0);
        break;
      case 'revision':
        $versionArr = VersionHelper::nextVersion($projectId, 0, 0, 0, 1);
        break;
      default:
        $versionArr = VersionHelper::nextVersion($projectId, 0, 0, 0, 1);
    }

    return $versionArr;
  }

  public static function getVersionString($version)
  {
    return $version->major . '.' . $version->minor .
    '.' . $version->build . '.' . $version->revision;
  }
}
