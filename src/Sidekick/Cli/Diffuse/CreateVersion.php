<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;
use Cubex\Log\Log;
use Sidekick\Components\Diffuse\Enums\VersionNumberType;
use Sidekick\Components\Enums\ApprovalState;
use Sidekick\Components\Diffuse\Enums\VersionType;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Components\Fortify\FortifyHelper;
use Sidekick\Components\Fortify\Mappers\Build;
use Sidekick\Components\Fortify\Mappers\BuildRun;
use Sidekick\Components\Fortify\Mappers\BuildsProjects;
use Sidekick\Components\Repository\Mappers\Commit;
use Symfony\Component\Process\Process;

class CreateVersion extends CliCommand
{
  /**
   * Source build run to create version from
   *
   * @valuerequired
   */
  public $buildId; //Possibly look into building from repo?

  /**
   * @valuerequired
   */
  public $projectId;
  /**
   * @valuerequired
   */
  public $incrementType = VersionNumberType::BUILD;
  /**
   * @valuerequired
   */
  public $type = 'std';
  /**
   * @valuerequired
   */
  public $version;

  public $dryRun;

  protected $_echoLevel = 'debug';

  public function execute()
  {
    $version = new Version($this->version);

    //Allow updating of build ID
    if($this->buildId !== null)
    {
      $version->buildId = $this->buildId;
    }

    if(!$version->exists())
    {
      if($this->version !== null)
      {
        throw new \Exception(
          "You have specified an invalid version: " . $this->version
        );
      }

      if($this->buildId === null)
      {
        throw new \Exception("You must specify a build ID");
      }

      if($this->projectId === null)
      {
        throw new \Exception("You must specify a project ID");
      }

      $this->incrementType = VersionNumberType::fromValue($this->incrementType);
      $incr                = [
        'major' => 0,
        'minor' => 0,
        'build' => 0,
        'rev'   => 0
      ];

      switch((string)$this->incrementType)
      {
        case VersionNumberType::MAJOR:
          $incr['build'] = 1;
          break;
        case VersionNumberType::MINOR:
          $incr['minor'] = 1;
          break;
        case VersionNumberType::BUILD:
          $incr['build'] = 1;
          break;
        case VersionNumberType::REVISION:
          $incr['rev'] = 1;
          break;
        default:
          $incr['build'] = 1;
          break;
      }

      list($major, $minor, $build, $revision) = VersionHelper::nextVersion(
        $this->projectId,
        $incr['major'],
        $incr['minor'],
        $incr['build'],
        $incr['rev']
      );

      $versionFormat = "$major.$minor.$build";
      $this->type    = VersionType::fromValue($this->type);
      if($revision > 0 && (string)$this->type !== VersionType::STANDARD)
      {
        $versionFormat .= '-' . $this->type . $revision;
      }

      //Exit out of the process, allowing to check a version create is possible
      if($this->dryRun)
      {
        return true;
      }

      Log::info("New Version: $versionFormat");

      $version->major     = $major;
      $version->minor     = $minor;
      $version->build     = $build;
      $version->revision  = $revision;
      $version->type      = $this->type;
      $version->projectId = $this->projectId;
      $version->saveChanges();

      Log::info("Version Created: ID " . $version->id());
    }

    $reattempt = "\n\nPlease re-attempt with:\n" .
      "./cubex Diffuse.CreateVersion --version=" . $version->id();

    $sourceDir = VersionHelper::sourceLocation($version);

    if(!file_exists($sourceDir))
    {
      if(mkdir($sourceDir, 0777, true))
      {
        Log::info("Created Dir: " . VersionHelper::sourceLocation($version));
      }
      else
      {
        throw new \Exception(
          "Version " . $version->id() . " has been created, " .
          "however, the source directory '" . $sourceDir . "' could not. " .
          $reattempt
        );
      }
      $sourceDir = realpath($sourceDir);
    }

    $buildRun = new BuildRun($version->buildId);
    if(!$buildRun->exists())
    {
      throw new \Exception(
        "The build ID you specified does not exist." . $reattempt
      );
    }
    else
    {
      if($version->repoId < 1)
      {
        $buildProject = new BuildsProjects(
          [$buildRun->buildId, $version->projectId]
        );
        if($buildProject->exists())
        {
          $version->repoId = $buildProject->buildSourceId;
        }
      }

      if($version->toCommitHash === null)
      {
        $version->toCommitHash = $buildRun->commitHash;
      }

      if($version->fromCommitHash === null)
      {
        //Locate previous approved version for commit hash
        $lastVersion = Version::collection(
          [
            "projectId"    => $version->projectId,
            "versionState" => ApprovalState::APPROVED,
          ]
        )->setLimit(0, 1)
          ->setOrderByQuery(
            "major DESC, minor DESC, build DESC, revision DESC, created_at DESC"
          );
        if($lastVersion->hasMappers())
        {
          $version->fromCommitHash = $lastVersion->first()->toCommitHash;
        }
      }

      if($version->fromCommitHash === $version->toCommitHash)
      {
        $version->changeLog = 'No Changes';
      }
      else if(
        $version->changeLog === null &&
        $version->fromCommitHash !== null &&
        $version->toCommitHash !== null
      )
      {
        //TODO get changes from git directly
        //git log --online fromCommitHash^..toCommitHash

        $commits = Commit::collectionBetween(
          $version->fromCommitHash,
          $version->toCommitHash,
          Commit::INCLUDE_LATEST
        );

        if($commits->hasMappers())
        {
          $changes = [];
          foreach($commits as $commit)
          {
            $changes[] = $commit->subject .
              (empty($commit->message) ? "" : "\n$commit->message");
          }

          //Switch to date ordered
          $changes = array_reverse($changes);

          $version->changeLog = implode("\n", $changes);
        }
      }

      $version->saveChanges();
    }

    if($buildRun->projectId != $version->projectId)
    {
      throw new \Exception(
        "The build ID specified is not within" .
        " the same project as the version." . $reattempt
      );
    }

    $build = new Build($buildRun->buildId);
    if(!$build->exists())
    {
      throw new \Exception(
        "The build ID specified has an invalid build." . $reattempt
      );
    }

    $buildSource = realpath(
      build_path(
        FortifyHelper::buildPath($buildRun->id()),
        $build->sourceDirectory
      )
    );

    if(!file_exists($buildSource))
    {
      throw new \Exception(
        "The build source directory could not be found " .
        "'" . $buildSource . "'. " . $reattempt
      );
    }
    else
    {
      $command = 'rsync -av --exclude=.git --exclude=.gitignore '
        . '--delete --delete-excluded '
        . escapeshellarg($buildSource . '/') . ' ' . escapeshellarg($sourceDir);

      Log::info($command);
      $process = new Process($command);
      $process->setTimeout(600);
      $process->setIdleTimeout(600);
      $process->run();
      Log::debug($process->getOutput());
    }
    return true;
  }

  public function copyDirectory($source, $destination)
  {
    if(is_dir($source))
    {
      if(!file_exists($destination))
      {
        mkdir($destination);
      }

      $pass = true;

      $directory = dir($source);
      while(false !== ($readdirectory = $directory->read()))
      {
        if($readdirectory == '.' || $readdirectory == '..')
        {
          continue;
        }
        $PathDir = $source . '/' . $readdirectory;
        if(is_dir($PathDir))
        {
          $pass = $this->copyDirectory(
            $PathDir,
            ($destination . '/' . $readdirectory)
          );
          continue;
        }
        $loopPass = copy($PathDir, $destination . '/' . $readdirectory);
        if(!$loopPass)
        {
          Log::debug(
            "Unable to copy from '$PathDir' to '$destination/$readdirectory''"
          );
          $pass = false;
        }
      }

      $directory->close();
    }
    else
    {
      $pass = copy($source, $destination);
    }
    return $pass;
  }

  public function latest()
  {
    echo VersionHelper::latestVersions($this->project, 1)->first()->format();
  }
}
