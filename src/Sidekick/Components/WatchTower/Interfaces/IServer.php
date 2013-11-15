<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\WatchTower\Interfaces;

interface IServer
{
  public function getHostname();

  public function getIpv4();

  public function getIpv6();

  /**
   * Store running processes for a specific process name
   *
   * @param $process
   * @param $count
   *
   * @return mixed
   */
  public function storeProcessCount($process, $count);
}
