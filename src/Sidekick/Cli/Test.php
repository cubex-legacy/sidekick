<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli;

use Cubex\Cli\CliCommand;
use Sidekick\Components\WatchTower\Mappers\Statistics\StateHistory;
use Sidekick\Components\WatchTower\Mappers\Statistics\Summary;

class Test extends CliCommand
{
  /**
   * @return int
   */
  public function execute()
  {
    $history                 = new StateHistory();
    $history->checkId        = 1;
    $history->serverId       = 2;
    $history->stateTime      = date("Y-m-d-h-i");
    $history->executionTime  = 12;
    $history->rawResponse    = "fsejkhewkqhwflqw fklqhwe gqweh gkhqwe gkwhq";
    $history->stateTimestamp = time();
    $history->state          = 1;

    $history->returnCode          = 3;
    $history->startTime           = time();
    $history->endTime             = time() + 12;
    $history->checkServerHostname = 'tester';
    $history->saveChanges();

    Summary::storeValue(1, "state", 1);
    Summary::storeValue(1, "uptime", 1.001);
  }
}
