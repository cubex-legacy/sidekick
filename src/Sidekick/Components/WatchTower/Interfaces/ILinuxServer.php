<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower\Interfaces;

interface ILinuxServer extends IServer
{
  /**
   * @param string $loadAverage e.g. "1.0 0.0 0.0"
   *
   * @return mixed
   */
  public function storeCurrentLoadAverage($loadAverage);
}
