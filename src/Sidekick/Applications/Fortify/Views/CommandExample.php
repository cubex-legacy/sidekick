<?php
/**
 * @author: davide.argellati
 *        Application: Fortify
 */
namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;
use Sidekick\Components\Fortify\Mappers\BuildCommand;

class CommandExample extends ViewModel
{
  protected $_mapper;
  protected $_float;

  public function __construct(BuildCommand $mapper, $float = true)
  {
    $this->_mapper = $mapper;
    $this->_float  = $float;
  }

  public function render()
  {
    $out = new HtmlElement('strong', [], $this->_mapper->command);
    foreach($this->_mapper->args as $arg)
    {
      $out .= '&nbsp;&nbsp;' . $arg . '';
    }
    if(!empty($this->_mapper->successExitCodes))
    {
      $out .= '<br /><br />Success Exit Codes: ';

        $out .= implode('&nbsp;&nbsp; , &nbsp;&nbsp;', $this->_mapper->successExitCodes);

    }
    $pull = $this->_float ? 'pull-right span6' : '';
    $wrap = new HtmlElement('div', ['class' => "well $pull"], $out);
    return $wrap;
  }
}
