<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Cli\CliCommand;
use Sidekick\Components\Diffuse\Enums\VersionType;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Deployment\Rsync\RsyncService;

class VersionCheck extends CliCommand
{
  /**
   * @valuerequired
   * @required
   */
  public $project;
  /**
   * @valuerequired
   */
  public $build = 0;
  /**
   * @valuerequired
   */
  public $minor = 0;
  /**
   * @valuerequired
   */
  public $major = 0;
  /**
   * @valuerequired
   */
  public $revision = 0;
  /**
   * @valuerequired
   */
  public $type = 'std';

  /**
   * @return int
   */
  public function execute()
  {
    $this->type = VersionType::fromValue($this->type);
    //(new DebuggerBundle())->init();
    echo "\nVersion Check: ";
    list($major, $minor, $build, $revision) = VersionHelper::nextVersion(
      $this->project,
      $this->major,
      $this->minor,
      $this->build,
      $this->revision
    );

    echo "$major.$minor.$build";
    if($revision > 0 && (string)$this->type !== VersionType::STANDARD)
    {
      echo '-' . $this->type . $revision;
    }

    echo "\n";

    $version            = new Version();
    $version->major     = $major;
    $version->minor     = $minor;
    $version->build     = $build;
    $version->revision  = $revision;
    $version->type      = $this->type;
    $version->projectId = $this->project;

    echo "Location: " . VersionHelper::sourceLocation($version);

    echo "\n";
  }

  public function latest()
  {
    echo VersionHelper::latestVersions($this->project, 1)->first()->format();
  }
}
