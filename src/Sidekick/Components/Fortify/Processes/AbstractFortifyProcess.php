<?php
namespace Sidekick\Components\Fortify\Processes;

use Sidekick\Components\Fortify\AbstractFortifyElement;

abstract class AbstractFortifyProcess extends AbstractFortifyElement
  implements FortifyProcess
{
  protected $_log = [];

  protected function _writeToLog($line)
  {
    $this->_log[] = $line;
  }

  protected function _writeLogLine($line)
  {
    $this->_writeToLog($line . "\n");
  }

  public function getLog()
  {
    return implode("", $this->_log);
  }
}
