<?php
/**
 * @author: brooke.bryan
 *        Application: Scripture
 */
namespace Sidekick\Applications\Scripture\Views;

use Cubex\Helpers\Strings;
use Cubex\View\HtmlElement;
use Cubex\View\ViewModel;
use Sidekick\Components\Scripture\Markdown\SiteMarkdown;

class Scripture extends ViewModel
{
  protected $_sidebarMd;
  protected $_contentMd;
  protected $_mdEngine;

  public function __construct($sidebarMarkdown, $contentMarkdown)
  {
    $this->_contentMd = $contentMarkdown;
    $this->_sidebarMd = $sidebarMarkdown;
    $this->_mdEngine  = new SiteMarkdown();
    $this->requireJs(
      'https://google-code-prettify.googlecode.com/svn/'
      . 'loader/run_prettify.js?skin=desert'
    );
  }

  public function render()
  {
    $content = $this->_mdEngine->transformMarkdown($this->_contentMd);
    if($this->_sidebarMd === null)
    {
      return $content;
    }
    else
    {
      $sidebar = $this->_mdEngine->transformMarkdown($this->_sidebarMd);
      $wrapper = new HtmlElement('div', ['class' => 'row-fluid']);
      $wrapper->nestElement('div', ['class' => 'span2'], $sidebar);
      $wrapper->nestElement('div', ['class' => 'span10'], $content);
      return $wrapper;
    }
  }
}
