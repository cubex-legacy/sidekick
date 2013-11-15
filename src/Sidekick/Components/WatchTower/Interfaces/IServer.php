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
}
