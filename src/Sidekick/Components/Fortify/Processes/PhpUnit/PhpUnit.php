<?php
namespace Sidekick\Components\Fortify\Processes\PhpUnit;

use Cubex\Log\Log;
use Sidekick\Components\Fortify\Processes\AbstractFortifyProcess;
use Symfony\Component\Process\Process;

class PhpUnit extends AbstractFortifyProcess
{
  /**
   * @param $stage string Stage the process is running in e.g. Install
   *
   * @return bool|int Exit code, or true for success
   */
  public function process($stage)
  {
    $command = 'phpunit';

    $command .= " --coverage-clover ";
    $command .= build_path($this->_scratchPath, 'clover.sml');

    $command .= " --log-json  ";
    $command .= build_path($this->_scratchPath, 'phpunit.json');

    $command .= " -c ";
    $command .= build_path($this->_basePath, 'phpunit.xml.dist');

    $process = new Process($command);
    $process->setWorkingDirectory($this->_basePath);
    $process->run();

    Log::debug($process->getOutput());

    return $process->getExitCode();
  }
}