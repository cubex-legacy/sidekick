<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Evento\Widgets;

use Cubex\Helpers\Strings;
use Cubex\View\HtmlElement;
use Cubex\View\RenderGroup;
use Cubex\View\Widgets\Widget;
use Sidekick\Components\Enums\Severity;

class SeverityWidget extends Widget
{
  protected $_severity;

  public function __construct($severity = 1)
  {
    $this->_severity = $severity;
  }

  public function render()
  {
    $height = (20 * $this->_severity) - 5;
    $margin = 104 - (20 * $this->_severity);

    $severityList = array_reverse((new Severity())->getConstList());
    $key          = new HtmlElement(
      "ul",
      ['style' => 'list-style-type: none; margin:0;']
    );
    foreach($severityList as $k => $v)
    {
      $line = "$v - " . Strings::titleize($k);
      if($v === $this->_severity)
      {
        $line = new HtmlElement('strong', [], $line);
      }
      $key->nestElement("li", [], $line);
    }

    switch($this->_severity)
    {
      case 1:
        $progress = 'progress-success';
        break;
      case 2:
        $progress = 'progress-info';
        break;
      case 3:
        $progress = 'progress-warning';
        break;
      case 4:
      case 5:
        $progress = 'progress-danger';
        break;
    }

    return new HtmlElement(
      'div',
      [
      'class' => 'progress ' . $progress,
      'style' => 'height:140px; padding:0 10px 10px;'
      ],
      new RenderGroup(
        new HtmlElement('h4', [], 'Severity'),
        new HtmlElement(
          'div',
          [
          'class' => 'bar',
          'style' => "width: 30px; height:" . $height . "px; " .
          "float:right; margin-top:" . $margin . "px;"
          ]
        ),
        $key
      )
    );
  }
}
