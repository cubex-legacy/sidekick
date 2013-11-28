<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Notifiers;

use Sidekick\Applications\BaseApp\SidekickApplication;
use Sidekick\Components\Notify\Interfaces\INotify;
use Sidekick\Components\Notify\Interfaces\INotifyMessage;

class Email implements INotify
{

  public function __construct()
  {
  }

  public function getName()
  {
    return "E-Mail";
  }

  public function setEventData($data)
  {
  }

  public function notify(
    $userId, INotifyMessage $message, SidekickApplication $app = null
  )
  {
    //Whatever we do to notify
  }
}
