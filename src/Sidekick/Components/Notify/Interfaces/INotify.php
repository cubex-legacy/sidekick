<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Interfaces;

use Sidekick\Applications\BaseApp\SidekickApplication;

interface INotify
{
  public function getName();

  public function setEventData($data);

  public function notify(
    $userId, INotifyMessage $message, SidekickApplication $app = null
  );
}
