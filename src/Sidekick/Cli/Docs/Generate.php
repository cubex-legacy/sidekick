<?php
/**
 * Author: oke.ugwu
 * Date: 17/07/13 16:10
 */

namespace Sidekick\Cli\Docs;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Helpers\System;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\Version;
use Symfony\Component\Process\Process;

class Generate extends CliCommand
{
  /**
   * @required
   * @valuerequired
   */
  public $versionId;

  public $docBase = "docs/";

  public function execute()
  {
    $version = new Version($this->versionId);

    //generate docs
    $vendor_dir = build_path(dirname(WEB_ROOT), 'vendor');
    $command    = $vendor_dir . '/bin/apigen.php';
    if(System::isWindows())
    {
      $command .= '.bat';
    }
    $command .= " --source=";
    $command .= VersionHelper::sourceLocation($version);
    $command .= ' --exclude "*/vendor/*"';
    $command .= " --destination={$this->docBase}$this->versionId";

    echo $command . PHP_EOL;

    $process = new Process($command);
    $process->run();
  }
}
