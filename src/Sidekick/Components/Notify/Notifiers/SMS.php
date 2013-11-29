<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Notifiers;

use Sidekick\Components\Notify\Interfaces\INotify;
use Sidekick\Components\Notify\Interfaces\INotifyMessage;
use Sidekick\Components\Users\Mappers\User;

class SMS implements INotify
{
  public function __construct()
  {
  }

  public function getName()
  {
    return "Text Message";
  }

  public function setEventData($data)
  {
  }

  public function notify(User $user, INotifyMessage $message)
  {
    //Whatever we do to notify
  }
}
