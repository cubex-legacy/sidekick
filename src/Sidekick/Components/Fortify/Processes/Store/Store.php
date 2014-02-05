<?php
namespace Sidekick\Components\Fortify\Processes\Store;

use Cubex\Log\Log;
use Sidekick\Components\Fortify\Processes\AbstractFortifyProcess;
use Symfony\Component\Process\Process;

class Store extends AbstractFortifyProcess
{
  /**
   * @param $stage string Stage the process is running in e.g. Install
   *
   * @return bool|int Exit code, or true for success
   */
  public function process($stage)
  {
    $fileName = $this->_commitHash . ".tar.gz";

    if(!file_exists($this->_getStorageFolder()))
    {
      mkdir($this->_getStorageFolder());
    }

    $zipLoc = build_path(
      CUBEX_PROJECT_ROOT,
      $this->_getStorageFolder(),
      $fileName
    );

    $command = "tar -cvzf $zipLoc $this->_basePath";
    Log::info("Running " . $command);

    $process = new Process($command);
    $process->run();

    Log::debug($process->getOutput());

    return $process->getExitCode();
  }

  protected function _getStorageFolder()
  {
    return build_path('fortify', 'b' . $this->_branch->id(), 'archive');
  }
}
