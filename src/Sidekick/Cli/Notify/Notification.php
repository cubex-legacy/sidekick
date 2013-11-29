<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Cli\Notify;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Cli\CliCommand;
use Cubex\Cli\PidFile;
use Cubex\Log\Log;
use Cubex\Facade\Queue;
use Cubex\Queue\CallableQueueConsumer;
use Cubex\Queue\StdQueue;
use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Components\Notify\Interfaces\INotify;
use Sidekick\Components\Notify\Mappers\Subscription;
use Sidekick\Components\Notify\NotifyMessage;
use Sidekick\Components\Users\Mappers\User;

class Notification extends CliCommand
{
  public function init()
  {
    $d = new DebuggerBundle();
    $d->init();
  }

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

  public function sendNotification($queue, $data)
  {
    $appName       = $data->appName;
    $eventKey      = $data->event;
    $subscriptions = Subscription::collection(
      ['app' => $appName, 'eventKey' => $eventKey]
    );

    $notifyList = [];
    $eventData  = (array)$data->data;
    foreach($subscriptions as $s)
    {
      $filters = $s->filters;
      $notify  = true;
      foreach($filters as $filter)
      {
        if(isset($eventData[$filter->name]))
        {
          if($eventData[$filter->name] != $filter->value)
          {
            $notify = false;
          }
        }
      }

      if($notify)
      {
        $notifyList[$s->userId] = $s->contactMethod;
      }
    }

    foreach($notifyList as $userId => $contactMethod)
    {
      if(class_exists($contactMethod))
      {
        $contactMethod = new $contactMethod;
        if($contactMethod instanceof INotify)
        {
          $user = new User($userId);
          echo "Sending " . $contactMethod->getName() . " to "
            . $user->email . PHP_EOL;
          try
          {
            $contactMethod->notify(
              $user,
              unserialize($data->msg)
            );
          }
          catch(\Exception $e)
          {
            echo $e->getMessage().PHP_EOL;
          }
        }
      }
      else
      {
        echo "Invalid Contact Method selected for $userId" . PHP_EOL;
      }
    }
  }
}
