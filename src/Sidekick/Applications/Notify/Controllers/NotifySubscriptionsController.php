<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Sidekick\Applications\Notify\Views\MySubscriptions;
use Sidekick\Components\Notify\Mappers\Subscription;

class NotifySubscriptionsController extends BaseNotifyController
{
  public function renderIndex()
  {
    $subscriptions = Subscription::collection(
      ['user_id' => \Auth::user()->getId()]
    );
    return new MySubscriptions($subscriptions, $this->_notifiableApps);
  }

  public function renderUnsubscribe()
  {
    $id           = $this->getStr('id');
    $subscription = new Subscription($id);
    $subscription->delete();

    $msg = new TransportMessage(
      'success', 'Your have successfully un-subscribed from event'
    );

    Redirect::to($this->baseUri())->with('msg', $msg)->now();
  }

  public function getRoutes()
  {
    return [
      ':id/unsubscribe' => 'unsubscribe'
    ];
  }
}
