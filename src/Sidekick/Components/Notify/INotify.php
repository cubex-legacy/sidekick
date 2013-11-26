<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify;

interface INotify
{
  public function getName();

  public function setEventData($data);

  public function notify();
}
