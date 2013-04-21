<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Facade\Queue;
use Cubex\Helpers\System;
use Cubex\Queue\CallableQueueConsumer;
use Cubex\Queue\StdQueue;

/**
 * Run Build Queue
 */
class BuildQueue extends CliCommand
{
  public function execute()
  {
    if(!System::isWindows())
    {
      declare(ticks = 1);
      pcntl_signal(SIGINT, array($this, "exited"));
      pcntl_signal(SIGTERM, array($this, "exited"));
      pcntl_signal(SIGHUP, array($this, "exited"));
      pcntl_signal(SIGUSR1, array($this, "exited"));
      pcntl_signal(SIGKILL, array($this, "exited"));
    }

    $queue = new StdQueue('BuildRequest');
    Queue::consume(
      $queue,
      new CallableQueueConsumer([$this, 'runBuild'], 10)
    );
  }

  public function runBuild($queue, $data)
  {
    $cwd     = getcwd();
    $rawArgs = ['Diffuse.Build', '-b', '1', '-p', $data->respositoryId];
    $build   = new Build($this->_loader, $rawArgs);
    $build->execute();
    chdir($cwd);
    return true;
  }

  public function exited()
  {
    echo Shell::colourText("\nLeaving Session\n", Shell::COLOUR_FOREGROUND_RED);
    die;
  }
}
