<?php
/**
 * Author: oke.ugwu
 * Date: 26/11/13 13:55
 */

namespace Sidekick\Applications\Notify\Controllers;

use Cubex\View\RenderGroup;
use Sidekick\Applications\BaseApp\Controllers\BaseControl;
use Sidekick\Components\Notify\Interfaces\INotifiableApp;

class BaseNotifyController extends BaseControl
{
  protected $_notifiableApps = [];

  public function preRender()
  {
    parent::preRender();
    $apps = $this->application()->project()->getApplications();
    foreach($apps as $app)
    {
      if($app instanceof INotifiableApp)
      {
        $this->_notifiableApps[$app->name()] = $app;
      }
    }
  }

  public function getSidebar()
  {
    return null;
  }
}
