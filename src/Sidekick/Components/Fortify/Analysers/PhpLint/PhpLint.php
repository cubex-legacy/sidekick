<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Analysers\PhpLint;

use Sidekick\Components\Fortify\Analysers\AbstractAnalyser;
use Symfony\Component\Process\Process;

class PhpLint extends AbstractAnalyser
{
  protected $_pattern = '.*\.php$';

  protected $_fileResults = [];

  public function configure($configuration)
  {
    if(is_scalar($configuration))
    {
      $this->_pattern = $configuration;
    }
    else
    {
      $this->_pattern = idx($configuration, 'pattern', '.*\.php$');
    }
  }

  public function analyse()
  {
    $passed = true;

    foreach($this->_getFiles() as $file)
    {
      if(preg_match("/$this->_pattern/", $file))
      {
        $command = 'php -l ' . build_path($this->_basePath, $file);
        $process = new Process($command);
        $process->run();
        $this->_fileResults[$file] = $process->getExitCode();

        if($this->_fileResults[$file] !== 0)
        {
          $passed = false;
        }
        break;
      }
    }

    return $passed;
  }
}
