<?php
namespace Sidekick\Components\Fortify\Analysers;

use Sidekick\Components\Repository\Mappers\Commit;
use Symfony\Component\Process\Process;

abstract class AbstractFileGrepAnalyser extends AbstractAnalyser
{
  protected $_pattern = '.*\.php$';
  protected $_matchOn = [];

  public function configure($configuration)
  {
    if($configuration === null)
    {
      return;
    }
    else if(!is_array($configuration))
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
    $passed         = true;
    $locatedMatches = [];
    $located        = 0;

    foreach($this->_getRepositoryFiles() as $file)
    {
      if(preg_match("/$this->_pattern/", $file))
      {
        foreach($this->_matchOn as $match)
        {
          $process = new Process("grep -i -n '$match' $file");
          $process->run();
          if($process->getExitCode() === 0)
          {
            $fileName = substr($file, strlen($this->_basePath) + 1);

            $matches = phutil_split_lines($process->getOutput(), false);
            foreach($matches as $line)
            {
              list($lineNumber, $todo) = explode(':', $line, 2);
              if($lineNumber > 0)
              {
                $locatedMatches[$fileName][$lineNumber] = trim($todo);
                $located++;
              }
            }
          }
        }
      }
    }
    $this->_storeData(
      $this->_getMetric() . ".json",
      json_encode($locatedMatches)
    );

    $this->_trackInsight($this->_getMetric(), $located);

    return $passed;
  }

  protected function _getMetric()
  {
    return 'count';
  }
}
