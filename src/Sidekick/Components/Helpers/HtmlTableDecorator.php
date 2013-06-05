<?php
/**
 * @author  davide.argellati
 */

namespace Sidekick\Components\Helpers;

use Cubex\Helpers\Strings;
use Cubex\Text\BaseTextTableDecorator;

class HtmlTableDecorator extends BaseTextTableDecorator
{
  protected $_striped;
  protected $_bordered;
  protected $_hover;

  public function setStriped($isStriped = false)
  {
    $this->_striped = $isStriped;
  }

  public function setBordered($isBordered = false)
  {
    $this->_striped = $isBordered;
  }

  public function setHover($isHover = false)
  {
    $this->_hover = $isHover;
  }

  public function renderTopBorder()
  {
    $classes[] = 'table';

    if($this->_striped)
    {
      $classes[] = 'table-striped';
    }
    if($this->_bordered)
    {
      $classes[] = 'table-bordered';
    }
    if($this->_hover)
    {
      $classes[] = 'table-hover';
    }
    return '<table class="' . implode(' ', $classes) . '">';
  }

  public function renderBottomBorder()
  {

    return '</tbody></table>';
  }

  public function renderColumnHeaders(array $headers)
  {
    $out = "<thead><tr>";

    foreach($headers as $head)
    {
      $out .= '<th>' . Strings::titleize($head) . '</th>';
    }

    $out .= "</tr></thead><tbody>";
    return $out;
  }

  public function renderDataRow(array $data)
  {
    $out = "<tr>";

    foreach($data as $td)
    {
      $out .= "<td>$td</td>";
    }

    $out .= "</tr>";
    return $out;
  }

  public function renderSpacerRow()
  {
    return '<tr class="table-spacer"><td></td></tr>';
  }

  public function renderSubHeading($text)
  {
    return '<tr class="table-subheading"><td>$text</td></tr>';
  }
}
