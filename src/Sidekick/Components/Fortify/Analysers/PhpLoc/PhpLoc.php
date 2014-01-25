<?php
namespace Sidekick\Components\Fortify\Analysers\PhpLoc;

use Sidekick\Components\Fortify\Analysers\AbstractAnalyser;
use Sidekick\Components\Repository\Mappers\Commit;
use Symfony\Component\Process\Process;

class PhpLoc extends AbstractAnalyser
{
  /**
   * @param Commit $commit
   *
   * @return bool Completed Analysis
   */
  public function analyse(Commit $commit)
  {
    $logFile = build_path($this->_scratchPath, 'phploc.xml');

    $command = "phploc --log-xml $logFile --no-interaction ";
    $command .= $this->_basePath;

    $process = new Process($command);
    $process->setWorkingDirectory($this->_scratchPath);
    $process->run();

    if(file_exists($logFile))
    {
      $xml = file_get_contents($logFile);
      if(!empty($xml))
      {
        $log = new \SimpleXMLElement($xml);
        if(!empty($log))
        {
          foreach($log as $item => $value)
          {
            $this->_trackInsight($item, $value);
          }
        }
      }
    }

    return true;
  }
}
