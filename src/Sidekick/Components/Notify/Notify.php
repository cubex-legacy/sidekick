<?php
/**
 * Description
 *
 * @author oke.ugwu
 */

namespace Sidekick\Components\Notify;

use Cubex\Facade\Queue;
use Cubex\Queue\StdQueue;
use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Components\Notify\Interfaces\INotifiableApp;

class Notify
{

  /**
   * @param INotifiableApp|SidekickApplication $app
   * @param string                             $eventKey
   * @param array                              $eventData
   *
   * @throws \Exception
   */
  public static function trigger(
    INotifiableApp $app, $eventKey, $eventData = []
  )
  {
    //check that we have a valid eventKey
    $config     = $app->getNotifyConfig();
    $configItem = $config->getItem($eventKey);
    if($configItem == null)
    {
      throw new \Exception("Invalid notification Event Key");
    }

    $filters = $configItem->getFilters();
    //Do we have all the required data?
    foreach($filters as $name => $options)
    {
      if(!isset($eventData[$name]))
      {
        throw new \Exception("$eventKey requires the $name filter to be set");
      }
    }

    //All good, add to queue
    Queue::push(
      new StdQueue("notifyQueue"),
      [
      "appName"   => $app->name(),
      "event"     => $eventKey,
      "data"      => $eventData,
      "timestamp" => time()
      ]
    );
  }
}
