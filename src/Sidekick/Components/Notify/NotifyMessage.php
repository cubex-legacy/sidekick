<?php
/**
 * Author: oke.ugwu
 * Date: 24/10/13 14:27
 */

namespace Sidekick\Components\Notify;

class NotifyMessage implements INotifyMessage
{
  protected $_summary;
  protected $_message;
  protected $_subject;

  public function getSummary()
  {
    return $this->_summary;
  }

  public function getMessage()
  {
    return $this->_message;
  }

  public function getSubject()
  {
    return $this->_subject;
  }

  public function setSummary($text)
  {
    $this->_summary = $text;
  }

  public function setMessage($text)
  {
    $this->_message = $text;
  }

  public function setSubject($text)
  {
    $this->_subject = $text;
  }
}
