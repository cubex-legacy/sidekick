<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Cli\CliCommand;
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
   * @return int
   */
  public function execute()
  {
    (new DebuggerBundle())->init();
    echo "\nVersion Check: ";
    echo implode(
      ".",
      VersionHelper::nextVersion(
        $this->project,
        $this->major,
        $this->minor,
        $this->build
      )
    );
    echo "\n";
  }
}
