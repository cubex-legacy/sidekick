<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower;

use Cubex\Components\IComponent;

class WatchTowerComponent implements IComponent
{
  public function name()
  {
    return "Watch Tower";
  }

  public function description()
  {
    return "Server monitoring and data collection";
  }
}
