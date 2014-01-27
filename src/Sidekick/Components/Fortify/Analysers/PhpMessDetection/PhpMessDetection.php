<?php
namespace Sidekick\Components\Fortify\Analysers\PhpMessDetection;

use Cubex\Log\Log;
use Sidekick\Components\Fortify\Analysers\AbstractAnalyser;
use Sidekick\Components\Repository\Mappers\Commit;
use Symfony\Component\Process\Process;

class PhpMessDetection extends AbstractAnalyser
{
  /**
   * @param Commit $commit
   *
   * @return bool Completed Analysis
   */
  public function analyse(Commit $commit)
  {
    $logFile = build_path($this->_scratchPath, 'pmd.report.xml');

    $command = "phpmd " . build_path($this->_basePath, "src") . " xml ";
    $command .= build_path($this->_basePath, "phpmd.xml");
    $command .= " --reportfile $logFile";

    Log::debug($command);

    $process = new Process($command);
    $process->setWorkingDirectory($this->_scratchPath);
    $process->run();

    if(file_exists($logFile))
    {
      $xml = file_get_contents($logFile);
      if(!empty($xml))
      {
        $this->_storeData("pmd.report.xml", $xml);
      }
    }

    return $process->getExitCode() === 0;
  }
}
