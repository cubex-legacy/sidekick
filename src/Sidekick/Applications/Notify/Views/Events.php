<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Views;


use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Notify\Mappers\EventTypes;

class Events extends TemplatedViewModel
{
  public function getEventTypes()
  {
    $events = EventTypes::collection()->loadAll();
    return ($events->count() > 0) ? $events : [];
  }
}
