<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Analysers\PhpLint;

use Cubex\Log\Log;
use Sidekick\Components\Fortify\Analysers\AbstractAnalyser;
use Sidekick\Components\Repository\Mappers\Commit;
use Symfony\Component\Process\Process;

class PhpLint extends AbstractAnalyser
{
  protected $_pattern = '.*\.php$';

  protected $_fileResults = [];

  public function configure($configuration)
  {
    if(!is_array($configuration))
    {
      $this->_pattern = $configuration;
    }
    else
    {
      $this->_pattern = idx($configuration, 'pattern', '.*\.php$');
    }
  }

  public function analyse(Commit $commit)
  {
    $failed = 0;
    $passed = true;

    foreach($this->_getCurrentFiles($commit) as $file)
    {
      if(preg_match("/$this->_pattern/", $file->filePath))
      {
        $command = 'php -l ' . build_path($this->_basePath, $file->filePath);
        Log::debug($command);
        $process = new Process($command);
        $process->run();

        if($process->getExitCode() !== 0)
        {
          $failedFiles[] = $file->filePath;
          $passed        = false;
          $failed++;
        }
      }
    }

    if(!empty($failedFiles))
    {
      $this->_storeData("failed.json", json_encode($failedFiles));
    }

    $this->_trackInsight("failed", $failed);

    return $passed;
  }
}
