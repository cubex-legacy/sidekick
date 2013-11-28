<?php
/**
 * Author: oke.ugwu
 * Date: 26/11/13 11:45
 */

namespace Sidekick\Components\Notify\Interfaces;

interface INotifiableApp
{
  /**
   * @return \Sidekick\Components\Notify\NotifyConfig
   */
  public function getNotifyConfig();
}
