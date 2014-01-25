<?php
namespace Sidekick\Components\Fortify\Analysers\Todos;

use Sidekick\Components\Fortify\Analysers\AbstractFileGrepAnalyser;

class Todos extends AbstractFileGrepAnalyser
{
  protected $_matchOn = ["\\btodo\\b.*", "\\bfixme\\b.*"];

  protected function _getMetric()
  {
    return 'todos';
  }
}
