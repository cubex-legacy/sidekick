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

  public $timeout = 120;

  public $docBase = "docs/";

  public function execute()
  {
    $version = new Version($this->versionId);

    //generate docs
    $vendorDir = build_path(dirname(WEB_ROOT), 'vendor');
    $command   = $vendorDir . '/bin/apigen.php';
    if(System::isWindows())
    {
      $command .= '.bat';
    }
    $command .= " --source=";
    $command .= VersionHelper::sourceLocation($version);
    $command .= " --destination={$this->docBase}$this->versionId";
    $command .= " --skip-doc-path=";
    $command .= VersionHelper::sourceLocation($version)."vendor\\*";

    echo $command . PHP_EOL;

    $process = new Process($command);
    $process->setTimeout($this->timeout);
    $process->run();
    $process->getOutput();
  }
}
