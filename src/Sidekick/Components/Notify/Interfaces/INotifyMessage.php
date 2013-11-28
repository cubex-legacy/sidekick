<?php
/**
 * Author: oke.ugwu
 * Date: 24/10/13 14:25
 */

namespace Sidekick\Components\Notify\Interfaces;

interface INotifyMessage
{
  /**
   * @return string
   */
  public function getSummary();

  /**
   * @return string
   */
  public function getMessage();

  /**
   * @return string
   */
  public function getSubject();
}
