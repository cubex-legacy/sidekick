<?php
namespace Sidekick\Components\Fortify\Processes\Command;

use Cubex\Log\Log;
use Sidekick\Components\Fortify\Processes\AbstractFortifyProcess;
use Symfony\Component\Process\Process;

class Command extends AbstractFortifyProcess
{
  /**
   * @param $stage string Stage the process is running in e.g. Install
   *
   * @return bool|int Exit code, or true for success
   */
  public function process($stage)
  {
    if(!is_array($this->_config))
    {
      $this->_config = ["Command" => $this->_config];
    }

    foreach($this->_config as $alias => $command)
    {
      Log::info("Running $alias '$command'");
      $process = new Process($command, $this->_basePath);
      $process->run();

      Log::debug($process->getOutput());

      if($process->getExitCode() !== 0)
      {
        Log::error($process->getErrorOutput());
        return $process->getExitCode();
      }
    }
    return true;
  }
}
