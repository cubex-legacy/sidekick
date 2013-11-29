<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Interfaces;

use Sidekick\Components\Users\Mappers\User;

interface INotify
{
  public function getName();

  public function setEventData($data);

  public function notify(User $user, INotifyMessage $message);
}
