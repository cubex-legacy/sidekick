<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Scripts\RawCommand;

use Cubex\Data\Handler\DataHandler;
use Sidekick\Components\Fortify\Scripts\FortifyScript;
use Symfony\Component\Process\Process;

class RawCommand implements FortifyScript
{
  protected $_commands;

  public function configure($configuration)
  {
    $config = new DataHandler();
    $config->setData("commands", $configuration);
    $this->_commands = $config->getArr("commands");
    return $this;
  }

  public function execute()
  {
    $passed = true;

    foreach($this->_commands as $command)
    {
      $process = new Process($command);
      $process->run();
      if($process->getExitCode() !== 0)
      {
        $passed = false;
      }
    }

    return $passed;
  }
}
