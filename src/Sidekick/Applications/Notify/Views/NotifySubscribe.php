<?php
/**
 * Author: oke.ugwu
 * Date: 26/11/13 14:55
 */

namespace Sidekick\Applications\Notify\Views;

use Cubex\Facade\Session;
use Cubex\Form\Form;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Notify\Enums\NotifyContactMethod;
use Sidekick\Components\Notify\Filters\AbstractFilter;
use Sidekick\Components\Users\Mappers\User;

class NotifySubscribe extends TemplatedViewModel
{
  protected $_app;
  protected $_eventKey;
  protected $_subscriptions;
  protected $_contactMethod;
  protected $_form;

  public function __construct($app, $eventKey, $subscriptions, $contactMethod)
  {
    $this->_app           = $app;
    $this->_eventKey      = $eventKey;
    $this->_subscriptions = $subscriptions;
    $this->_contactMethod = $contactMethod;
  }

  /**
   * @return \Sidekick\Components\Notify\Interfaces\INotifiableApp
   */
  public function getApp()
  {
    return $this->_app;
  }

  /**
   * @return string
   */
  public function getEventName()
  {
    return $this->getNotifyConfigItem()->eventName;
  }

  public function getEventKey()
  {
    return $this->_eventKey;
  }

  public function getSubscriptions()
  {
    return ($this->_subscriptions) ? $this->_subscriptions : [];
  }

  public function getContactMethod()
  {
    return $this->_contactMethod;
  }

  public function getUserContactMethods()
  {
    $contactMethods = [];
    $userId         = \Auth::user()->getId();
    $user           = new User($userId);
    if(!empty($user->email))
    {
      $contactMethods['EMAIL'] = NotifyContactMethod::EMAIL;
    }
    if(!empty($user->phoneNumber))
    {
      $contactMethods['SMS'] = NotifyContactMethod::SMS;
    }

    return $contactMethods;
  }

  public function getContactOptions()
  {
    $allContactMethods = (new NotifyContactMethod())->getConstList();
    return array_intersect($allContactMethods, $this->getUserContactMethods());
  }

  /**
   * @param AbstractFilter $filter
   *
   * @return mixed
   */
  public function getOptionValue($filter)
  {
    return $this->getNotifyConfigItem()->getFilter($filter->getName())
           ->getOptions()[$filter->getValue()];
  }

  public function getFilterValue($filterName)
  {
    if(isset($this->_subscriptions[$this->_eventKey]))
    {
      $filters = $this->_subscriptions[$this->_eventKey]['filters'];
      foreach($filters as $filter)
      {
        if($filter->name === $filterName)
        {
          return $filter->value;
        }
      }
    }

    return null;
  }

  public function getNotifyConfigItem()
  {
    return $this->getApp()->getNotifyConfig()->getItem($this->_eventKey);
  }

  public function form()
  {
    if($this->_form === null)
    {
      $this->_form = new Form('subscribe', $this->baseUri() . '/subscribe');
      $this->_form->setDefaultElementTemplate('{{input}}');
      $this->_form->addHiddenElement('app', $this->getApp()->name());
      $this->_form->addHiddenElement('eventKey', $this->getEventKey());
      $this->_form->addHiddenElement(
        'contactMethod',
        $this->getContactMethod()
      );
      $configItem = $this->getNotifyConfigItem();

      foreach($configItem->getFilters() as $filter)
      {
        $options = ['' => '--SELECT--'] + $filter->getOptions();
        $name    = "filters[" . $filter->getName() . "]";
        $this->_form->addSelectElement(
          $name,
          $options
        );
        $this->_form->getElement($name)->addAttribute(
          'class',
          'input-medium'
        );
      }

      $this->_form->addSubmitElement('Subscribe', 'subscribe');
      $this->_form->getElement('subscribe')->addAttribute(
        'class',
        'btn btn-success'
      );
    }

    return $this->_form;
  }

  public function getFlashMessage()
  {
    if(Session::getFlash('data'))
    {
      return idx(Session::getFlash('data'), 'msg');
    }

    return null;
  }
}
