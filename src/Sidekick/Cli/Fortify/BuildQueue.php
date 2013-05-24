<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Facade\Queue;
use Cubex\Helpers\System;
use Cubex\Log\Log;
use Cubex\Queue\CallableQueueConsumer;
use Cubex\Queue\StdQueue;
use Symfony\Component\Process\Process;

/**
 * Run Build Queue
 */
class BuildQueue extends CliCommand
{
  public $verbose;

  protected $_echoLevel = 'debug';

  public function execute()
  {
    Log::debug("Starting Queue Consumer");
    $queue = new StdQueue('BuildRequest');
    Queue::consume(
      $queue,
      new CallableQueueConsumer([$this, 'runBuild'], 10)
    );
    Log::debug("Completed Consume");
  }

  public function runBuild($queue, $data)
  {
    Log::debug("Entering Build Run for repo: " . $data->respositoryId);
    $cwd     = getcwd();
    $rawArgs = ['Fortify.Build', '-b', '1', '-p', $data->respositoryId];
    if($this->verbose)
    {
      $rawArgs[] = '-v';
    }
    Log::debug("Starting Build");
    //TODO: Run in separate process
    $build = new Build($this->_loader, $rawArgs);
    $build->execute();
    Log::debug("Executed Build");
    chdir($cwd);
    Log::debug("Completed Build Run");
    return true;
  }
}
