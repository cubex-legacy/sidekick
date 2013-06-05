<?php
/**
 * @author: davide.argellati
 *        Application: Fortify
 */
namespace Sidekick\Applications\BaseApp\Views;

use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;

class Alert extends ViewModel
{
  public $type;
  public $message;

  const TYPE_SUCCESS = 'success';
  const TYPE_ERROR   = 'error';
  const TYPE_INFO    = 'info';

  public function __construct($type, $message)
  {
    $this->type    = $type;
    $this->message = $message;
  }

  public function render()
  {
    if(!empty($this->message))
    {
      $class = $this->type != '' ? "alert-$this->type" : '';

      return new HtmlElement(
        'div',
        ['class' => "alert $class"],
        $this->message
      );
    }
    return '';
  }
}
