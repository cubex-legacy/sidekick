<?php
namespace Sidekick\Components\Fortify\Analysers\PhpCodeSniffer;

use Cubex\Log\Log;
use Sidekick\Components\Fortify\Analysers\AbstractAnalyser;
use Sidekick\Components\Repository\Mappers\Commit;
use Symfony\Component\Process\Process;

class PhpCodeSniffer extends AbstractAnalyser
{
  /**
   * @param Commit $commit
   *
   * @return bool Completed Analysis
   */
  public function analyse(Commit $commit)
  {
    $logFile = build_path($this->_scratchPath, 'phpcs.xml');

    $command = "phpcs --extensions=php -p ";
    $command .= "--report=checkstyle --warning-severity=0 ";
    $command .= "--report-file=$logFile ";
    $command .= "--standard=" . build_path($this->_basePath, "phpcs.xml");
    $command .= " " . build_path($this->_basePath, "src");

    Log::debug($command);
    $this->_writeToLog($command);

    $process = new Process($command);
    $process->setWorkingDirectory($this->_scratchPath);
    $process->run();

    $this->_writeToLog($process->getOutput());

    if(file_exists($logFile))
    {
      $xml = file_get_contents($logFile);
      if(!empty($xml))
      {
        $this->_storeData("phpcs.xml", $xml);
      }
    }

    return $process->getExitCode() === 0;
  }
}
