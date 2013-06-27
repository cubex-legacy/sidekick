<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Cli\CliCommand;
use Sidekick\Components\Diffuse\Enums\VersionType;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;

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
  public $type;


  /**
   * @return int
   */
  public function execute()
  {
    $this->type = VersionType::fromValue($this->type);
    (new DebuggerBundle())->init();
    echo "\nVersion Check: ";
    list($major, $minor, $build, $revision) = VersionHelper::nextVersion(
      $this->project,
      $this->major,
      $this->minor,
      $this->build,
      $this->revision
    );

    echo "$major.$minor.$build";
    if($revision > 0 && $this->type !== VersionType::STANDARD)
    {
      echo '-' . $this->type . $revision;
    }

    echo "\n";
  }
}
