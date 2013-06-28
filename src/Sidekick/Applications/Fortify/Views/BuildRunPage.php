<?php
/**
 * Author: oke.ugwu
 * Date: 26/06/13 15:06
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\HtmlElement;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class BuildRunPage extends ViewModel
{
  private $_view;
  private $_run;
  private $_build;
  private $_basePath;
  private $_currentTab;

  public function __construct($view, $run, $build, $basePath, $currentTab = '')
  {
    $this->_view       = $view;
    $this->_run        = $run;
    $this->_build      = $build;
    $this->_basePath   = $basePath;
    $this->_currentTab = $currentTab;
    $this->requireCss('buildDetailsView');
  }

  private function _header()
  {
    $resultDiv = new HtmlElement(
      'div',
      ['class' => 'pull-right build-result build-' . $this->_run->result],
      $this->_run->result
    );

    $pageHeader = new HtmlElement(
      'h1',
      [],
      'Build #' . $this->_run->id() . ' <small>' . ucfirst(
        $this->_build->name
      ) . ' Build</small>'
    );

    return new RenderGroup(
      $resultDiv,
      $pageHeader,
      new HtmlElement('div', ['class' => 'clearfix'], '')
    );
  }

  private function _tabs()
  {
    $tabs = [
      'Home'      => '',
      'Changes'   => 'changes',
      'Build Log' => 'buildlog'
    ];

    $tabItems = new Partial('<li class="%s"><a href="%s">%s</a></li>');
    foreach($tabs as $tab => $href)
    {
      $state = ("/$href" == $this->_currentTab) ? 'active' : '';
      $tabItems->addElement(
        $state,
        $this->_basePath . '/' . $href,
        $tab
      );
    }

    return new RenderGroup(
      '<ul class="nav nav-tabs">',
      $tabItems,
      '</ul>'
    );
  }

  public function render()
  {
    return new RenderGroup(
      $this->_header(),
      $this->_tabs(),
      $this->_view
    );
  }
}
