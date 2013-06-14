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
    Command $command, $commandRun, $passed = false, $commandOutput = null
  )
  {
    if($command->command !== null)
    {
      $obj                = new \stdClass();
      $obj->command       = $command;
      $obj->passed        = $passed;
      $obj->exitCode      = $commandRun['exit_code'];
      $obj->startTime     = $commandRun['start_time'];
      $obj->commandOutput = $commandOutput;
      $obj->argsLine      = '';
      if(is_array($command->args))
      {
        $obj->args = implode(' ', $command->args);
      }

      $this->_commandsRun[] = $obj;
    }

    return $this;
  }

  public function getCommandsRun()
  {
    return $this->_commandsRun;
  }
}
