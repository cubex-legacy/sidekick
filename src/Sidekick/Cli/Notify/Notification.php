<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Cli\Notify;

use Cubex\Cli\CliCommand;
use Cubex\Cli\PidFile;
use Cubex\Log\Log;
use Cubex\Facade\Queue;
use Cubex\Queue\CallableQueueConsumer;
use Cubex\Queue\StdQueue;

class Notification extends CliCommand
{
  public function execute()
  {
    $this->_pidFile = new PidFile();
    Log::debug("Starting Queue Consumer");
    $queue = new StdQueue('notifyQueue');
    Queue::consume(
      $queue,
      new CallableQueueConsumer([$this, 'sendNotification'], 10)
    );
    Log::debug("Completed Consume");
  }

  public function sendNotification($q, $data)
  {
    //Get event key and data

    //Check DB for hooks

    //Invoke (INotifiable) User or Group to get users to notify

    //Invoke (INotify) Email or SMS to actually send notification
  }
}
