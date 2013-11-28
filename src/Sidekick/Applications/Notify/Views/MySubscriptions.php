<?php
/**
 * Description
 *
 * @author oke.ugwu
 */

namespace Sidekick\Applications\Notify\Views;

use Cubex\View\TemplatedViewModel;

class MySubscriptions extends TemplatedViewModel
{
  protected $_subscriptions;
  /**
   * @var \Sidekick\Components\Notify\Interfaces\INotifiableApp[]
   */
  protected $_notifiableApps;

  public function __construct($subscriptions, $notifiableApps)
  {
    $this->_subscriptions  = $subscriptions;
    $this->_notifiableApps = $notifiableApps;
  }

  public function getSubscriptions()
  {
    return $this->_subscriptions;
  }

  public function getFilterOptions($subscription, $filter)
  {
    $app = $this->_notifiableApps[$subscription->app];
    $config = $app->getNotifyConfig();
    $configItem = $config->getItem($subscription->eventKey);
    return $configItem->getFilterOptions($filter->name)[$filter->value];
  }
}
