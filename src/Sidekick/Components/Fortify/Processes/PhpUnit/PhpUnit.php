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
    $cloverOut = build_path($this->_scratchPath, 'clover.sml');
    $command .= $cloverOut;

    $command .= " --log-json  ";
    $jsonLog = build_path($this->_scratchPath, 'phpunit.json');
    $command .= $jsonLog;

    $command .= " -c ";
    $command .= build_path($this->_basePath, 'phpunit.xml.dist');

    $this->_writeLogLine($command);

    $process = new Process($command);
    $process->setWorkingDirectory($this->_basePath);
    $process->run();

    if(file_exists($jsonLog))
    {
      $this->_storeData("phpunit.json", file_get_contents($jsonLog));
    }

    if(file_exists($cloverOut))
    {
      $this->_storeData("clover.sml", file_get_contents($cloverOut));
    }

    Log::debug($process->getOutput());

    $this->_writeToLog($process->getOutput());

    return $process->getExitCode();
  }
}
