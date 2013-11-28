<?php
/**
 * Author: oke.ugwu
 * Date: 26/11/13 12:39
 */

namespace Sidekick\Applications\Notify\Views;

use Cubex\View\TemplatedViewModel;

class NotifyIndex extends TemplatedViewModel
{
  protected $_apps;
  protected $_selectedAppName;

  public function __construct($apps, $selectedAppName)
  {
    $this->_apps            = $apps;
    $this->_selectedAppName = $selectedAppName;
  }

  /**
   * @return \Sidekick\Applications\BaseApp\BaseApp[]
   */
  public function getApps()
  {
    return $this->_apps;
  }

  /**
   * @return \Sidekick\Components\Notify\Interfaces\INotifiableApp
   */
  public function getSelectedApp()
  {
    if($this->_selectedAppName !== null)
    {
      return $this->_apps[$this->_selectedAppName];
    }

    return null;
  }
}
