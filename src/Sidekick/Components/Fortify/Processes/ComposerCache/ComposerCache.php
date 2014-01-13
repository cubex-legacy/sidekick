<?php
namespace Sidekick\Components\Fortify\Processes\ComposerCache;

use Cubex\Log\Log;
use Sidekick\Components\Fortify\Processes\AbstractFortifyProcess;
use Symfony\Component\Process\Process;

class ComposerCache extends AbstractFortifyProcess
{
  /**
   * @param $stage string Stage the process is running in e.g. Install
   *
   * @return bool|int Exit code, or true for success
   */
  public function process($stage)
  {
    switch($stage)
    {
      case 'install':
        return $this->install();
      case 'uninstall':
        return $this->storeCache();
    }
  }

  public function install()
  {
    $cacheDir = $this->_getCacheDirectory();
    Log::debug("Compose Cache Dir $cacheDir");
    if(file_exists($cacheDir))
    {
      $command = "rsync -lrtH $cacheDir/ $this->_basePath/vendor";
      Log::debug($command);
      $proc = new Process($command);
      $proc->run();
    }
    return true;
  }

  public function storeCache()
  {
    $cacheDir = $this->_getCacheDirectory();
    if(file_exists("$this->_basePath/vendor"))
    {
      $command = "rsync -lrtH $this->_basePath/vendor/ $cacheDir";
      Log::debug($command);
      $proc = new Process($command);
      $proc->run();
    }
    return true;
  }

  protected function _getCacheDirectory()
  {
    return build_path('fortify', 'b' . $this->_branch->id(), 'composerCache');
  }
}
