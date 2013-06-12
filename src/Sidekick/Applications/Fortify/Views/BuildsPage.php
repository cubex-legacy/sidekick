<?php
/**
 * Author: oke.ugwu
 * Date: 11/06/13 09:56
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\HtmlElement;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;

class BuildsPage extends ViewModel
{
  protected $_builds;
  protected $_buildRuns;
  protected $_projectId;
  protected $_buildType;


  public function __construct($projectId, $buildType, $builds, $buildRuns)
  {
    $this->_projectId = $projectId;
    $this->_builds    = $builds;
    $this->_buildRuns = $buildRuns;
    $this->_buildType = $buildType;
  }


  private function _tabs()
  {
    $tabItems = new Partial('<li class="%s"><a href="%s">%s</a></li>');
    foreach($this->_builds as $build)
    {
      $state = ($build->id() == $this->_buildType) ? 'active' : '';
      $tabItems->addElement(
        $state,
        '/fortify/' . $this->_projectId . '/' . $build->id(),
        $build->name
      );
    }

    return new RenderGroup(
      '<ul class="nav nav-tabs">',
      $tabItems,
      '</ul>'
    );
  }

  private function _buttonGroup()
  {
    $partial = new Partial(
      '<a class="btn" href="%s"><i class="icon-wrench"></i> %s</a>'
    );

    $repoLink = $this->_projectId . '/' . $this->_buildType . '/repository';
    $buttons  = [$repoLink => 'Repository'];

    foreach($buttons as $href => $txt)
    {
      $partial->addElement($this->baseUri() . '/' . ltrim($href, '/'), $txt);
    }

    return new RenderGroup(
      new HtmlElement('div', ['class' => "pull-right btn-group"], $partial)
    );
  }

  public function render()
  {
    return new RenderGroup(
      '<h1>Project Builds</h1>',
      $this->_buttonGroup(),
      $this->_tabs(),
      '<h1>Build History</h1>',
      new BuildRunsList($this->_buildRuns)
    );
  }
}
