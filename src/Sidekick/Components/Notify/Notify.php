<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify;

use Cubex\Facade\Auth;
use Cubex\Facade\Queue;
use Cubex\Queue\StdQueue;
use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Components\Notify\Mappers\EventTypes;

class Notify
{

  public static function trigger($eventKey, $eventData = [])
  {
    $evt = EventTypes::collection()->loadOneWhere(
      [
      "eventKey" => $eventKey
      ]
    );
    //Valid event type?
    if($evt == null)
    {
      throw new \Exception("Invalid notification type");
    }
    //Do we have all the required data?
    foreach($evt->eventParams as $requiredParam)
    {
      if(!isset($eventData[$requiredParam]))
      {
        throw new \Exception("$eventKey requires the $requiredParam parameter to be set");
      }
    }
    //All good, add to queue
    $q = new StdQueue("notifyQueue");
    Queue::push(
      $q,
      [
      "event" => $eventKey,
      "data" => $eventData,
      "timestamp" => time()
      ]
    );
  }

  public static function send(
    $userId, INotifyMessage $message, SidekickApplication $app = null
  )
  {
  }
}
