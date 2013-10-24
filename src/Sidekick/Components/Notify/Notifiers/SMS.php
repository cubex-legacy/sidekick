<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Notifiers;


use Sidekick\Components\Notify\INotify;

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
  public function notify()
  {
    //Whatever we do to notify
  }
}
