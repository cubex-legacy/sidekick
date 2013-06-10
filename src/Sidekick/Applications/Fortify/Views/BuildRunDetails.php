<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 10/06/13
 * Time: 10:44
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\Command;

class BuildRunDetails extends TemplatedViewModel
{
  protected $_commandsRun = [];

  public function addCommand(
    Command $command, $passed = false, $exitCode = 0, $commandOutput = null
  )
  {
    $obj = new \stdClass();
    $obj->command = $command;
    $obj->passed = $passed;
    $obj->exitCode = $exitCode;
    $obj->commandOutput = $commandOutput;

    $this->_commandsRun[] = $obj;
    return $this;
  }

  public function getCommandsRun()
  {
    return $this->_commandsRun;
  }
}
