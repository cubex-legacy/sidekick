<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Applications\Notify\Controllers;

use Cubex\Data\Transportable\TransportMessage;
use Cubex\Facade\Redirect;
use Cubex\Facade\Session;
use Cubex\View\RenderGroup;
use Sidekick\Applications\Notify\Views\NotifyIndex;
use Sidekick\Applications\Notify\Views\NotifySubscribe;
use Sidekick\Components\Notify\Mappers\Subscription;

class NotifyController extends BaseNotifyController
{
  public function renderIndex()
  {
    $appName = $this->getStr('appName', null);
    return new NotifyIndex($this->_notifiableApps, $appName);
  }

  public function renderEvents()
  {
    $appName  = $this->getStr('appName', null);
    $eventKey = $this->getStr('event');

    $contactMethod = $this->request()->postVariables('contactMethod');
    if(empty($contactMethod))
    {
      if(Session::getFlash('data'))
      {
        $contactMethod = idx(Session::getFlash('data'), 'contactMethod');
      }
    }

    $userId        = \Auth::user()->getId();
    $subscriptions = Subscription::collection(['user_id' => $userId]);

    if(!empty($contactMethod))
    {
      $condition = [
        'app'            => $appName,
        'event_key'      => $eventKey,
        'contact_method' => $contactMethod,
        'user_id'        => $userId
      ];

      $subscriptions = Subscription::collection($condition);
    }

    $subscriptions->setOrderBy('contactMethod');
    if($appName !== null)
    {
      $this->requireJs('subscribe');
      $app = $this->_notifiableApps[$appName];
      return new NotifySubscribe(
        $app, $eventKey, $subscriptions, $contactMethod
      );
    }
  }

  public function postSubscribe()
  {
    $postData = $this->request()->postVariables();

    //only save subscription if app is a valid notifiable app
    $isNotifiableApp = isset($this->_notifiableApps[$postData['app']]);
    if($isNotifiableApp && class_exists($postData['contactMethod']))
    {
      try
      {
        $filters                     = $this->_formatFilters(
          $postData['filters']
        );
        $subscription                = new Subscription();
        $subscription->app           = $postData['app'];
        $subscription->eventKey      = $postData['eventKey'];
        $subscription->contactMethod = $postData['contactMethod'];
        $subscription->userId        = \Auth::user()->getId();
        $subscription->filters       = $filters;
        $subscription->saveChanges();
      }
      catch(\Exception $e)
      {
        var_dump($e);
      }
    }

    $msg = new TransportMessage(
      'success', 'Your notification preference has been saved'
    );

    $data = [
      'msg'           => $msg,
      'contactMethod' => $postData['contactMethod']
    ];

    Redirect::to(
      $this->baseUri() . '/' . $postData['app'] . '/' . $postData['eventKey']
    )->with('data', $data)->now();
  }

  private function _formatFilters($filters)
  {
    $return = [];
    foreach($filters as $name => $value)
    {
      if($value == '')
      {
        throw new \Exception('Empty value');
      }
      $return[] = ['name' => $name, 'value' => $value];
    }

    return $return;
  }

  public function renderUnsubscribe()
  {
    $id           = $this->getStr('id');
    $subscription = new Subscription($id);
    $subscription->delete();

    $msg = new TransportMessage(
      'success', 'Your have successfully un-subscribed from event'
    );

    $data = [
      'msg'           => $msg,
      'contactMethod' => $subscription->contactMethod
    ];

    Redirect::to(
      $this->baseUri(
      ) . '/' . $subscription->app . '/' . $subscription->eventKey
    )->with('data', $data)->now();
  }

  public function getRoutes()
  {
    return [
      'subscribe'       => 'subscribe',
      ':id/unsubscribe' => 'unsubscribe',
      ':appName'        => 'index',
      ':appName/:event' => 'events',
    ];
  }
}
