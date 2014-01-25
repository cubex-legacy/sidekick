<?php
namespace Sidekick\Components\Fortify\Analysers\PhpSuperGlobals;

use Sidekick\Components\Fortify\Analysers\AbstractAnalyser;
use Sidekick\Components\Repository\Mappers\Commit;

class PhpSuperGlobals extends AbstractAnalyser
{
  protected $_pattern = '.*\.php$';
  protected $_matchOn = ["\\btodo\\b.*", "\\bfixme\\b.*"];

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
    //TODO: Add all todo's to a new Table for quick lookups and listing
    //Remove all dropped todos from the data source - list the commit and date

    $passed = true;

    foreach($this->_getCurrentFiles($commit) as $file)
    {
      if(preg_match("/$this->_pattern/", $file->filePath))
      {
        echo $file->filePath . "\n";
      }
    }

    $this->_trackInsight("todos", 6);

    return $passed;
  }
}
