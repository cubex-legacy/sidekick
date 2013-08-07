<?php
/**
 * Author: oke.ugwu
 * Date: 25/06/13 14:52
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\Helpers\DateTimeHelper;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\Command;

class BuildDetailsView extends TemplatedViewModel
{
  /**
   * @var \Sidekick\Components\Fortify\Mappers\BuildRun
   */
  public $run;
  public $basePath;
  protected $_commandsRun = [];

  public function __construct($run, $basePath)
  {
    $this->run      = $run;
    $this->basePath = $basePath;
  }

  public function addCommand(
    Command $command, $commandRun, $passed = false, $commandOutput = null
  )
  {
    if($command->command !== null)
    {
      $obj                  = new \stdClass();
      $obj->command         = $command;
      $obj->passed          = $passed;
      $obj->exitCode        = $commandRun['exit_code'];
      $obj->startTime       = $commandRun['start_time'];
      $obj->endTime         = $commandRun['end_time'];
      $this->_commandsRun[] = $obj;
    }

    return $this;
  }

  public function getCommandsRun()
  {
    return $this->_commandsRun;
  }

  public function getDuration($endDate, $startDate)
  {
    $diff = strtotime($endDate) - strtotime($startDate);
    return DateTimeHelper::formatTimespan($diff);
  }
}
