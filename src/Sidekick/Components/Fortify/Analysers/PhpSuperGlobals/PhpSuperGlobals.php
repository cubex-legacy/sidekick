<?php
namespace Sidekick\Components\Fortify\Analysers\PhpSuperGlobals;

use Sidekick\Components\Fortify\Analysers\AbstractAnalyser;
use Sidekick\Components\Repository\Mappers\Commit;
use Symfony\Component\Process\Process;

class PhpSuperGlobals extends AbstractAnalyser
{
  protected $_pattern = '.*\.php$';
  protected $_matchOn = ["\\btodo\\b.*", "\\bfixme\\b.*"];

  protected $_fileResults = [];

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
    $passed       = true;
    $locatedTodos = [];
    $todos        = 0;

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
                $locatedTodos[$fileName][$lineNumber] = trim($todo);
                $todos++;
              }
            }
          }
        }
      }
    }
    $this->_storeData("todos.json", json_encode($locatedTodos));
    $this->_trackInsight('todos', $todos);

    return $passed;
  }
}
