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

  public function notify(User $user, INotifyMessage $message)
  {
    try
    {
      \Cubex\Facade\Email::mail(
        $user->email,
        $message->getSubject(),
        $message->getMessage()
      );
    }
    catch(\Exception $e)
    {
      throw $e;
    }
  }
}
