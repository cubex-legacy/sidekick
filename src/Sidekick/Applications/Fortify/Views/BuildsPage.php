<?php
/**
 * Author: oke.ugwu
 * Date: 11/06/13 09:56
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\Facade\Session;
use Cubex\View\HtmlElement;
use Cubex\View\Impart;
use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Branch;

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
        ($this->appBaseUri() . '/' . $build->id()),
        $build->name
      );
    }

    return new RenderGroup(
      '<ul class="nav nav-tabs">',
      $tabItems,
      '</ul>'
    );
  }

  private function _buttonGroup($baseUri)
  {
    $partial = new Partial(
      '<a class="btn" href="%s"><i class="icon-wrench"></i> %s</a>'
    );

    $buttons = [
      $this->appBaseUri() . '/builds'   => 'Builds',
      $this->appBaseUri() . '/commands' => 'Commands',
    ];
    foreach($buttons as $href => $txt)
    {
      $partial->addElement($href, $txt);
    }

    return new RenderGroup(
      new HtmlElement('div', ['class' => "pull-right btn-group"], $partial)
    );
  }

  private function _filters($baseUri)
  {
    return new RenderGroup(
      '<small> ',
      new HtmlElement(
        'a',
        [
          'href'  => $baseUri . '/fail',
          'class' => "pull-right cushion"
        ],
        'Failed'
      ),
      new HtmlElement(
        'a',
        [
          'href'  => $baseUri . '/pass',
          'class' => "pull-right cushion"
        ],
        'Passed'
      ),
      new HtmlElement(
        'a',
        [
          'href'  => $baseUri . '/running',
          'class' => "pull-right cushion"
        ],
        'Running'
      ),
      new HtmlElement(
        'a',
        [
          'href'  => $baseUri,
          'class' => "pull-right cushion"
        ],
        'All'
      ),
      new HtmlElement(
        'strong',
        [
          'class' => "pull-right cushion"
        ],
        'Show:'
      ),
      '</small>'
    );
  }

  public function render()
  {
    $baseUri = $this->appBaseUri() . '/' . $this->_buildType;

    $alert = '';
    if(Session::getFlash('msg'))
    {
      $alert = new HtmlElement(
        'div',
        ['class' => 'alert alert-' . Session::getFlash('msg')->type],
        Session::getFlash('msg')->text
      );
    }

    $project = new Project($this->_projectId);
    $repo    = $project->repositories()->first();

    $branchAvailable = Branch::collection()
      ->loadWhere(['repository_id' => $repo->id()])
      ->getUniqueField('name');

    $buildLink       = $baseUri . '/build';
    $selectbranch    = $this->_getSelectBranch($branchAvailable, $buildLink);

    return new RenderGroup(
      '<h1>' . $project->name . ' Builds</h1>',
      //      $this->_buttonGroup($baseUri),
      $this->_tabs(),
      $alert,
      new HtmlElement(
        'a',
        [
          'href'                => '#run-build-modal',
          'class'               => 'btn btn-success pull-right',
          'data-remodal-target' => 'run-build-modal'
        ],
        'Run Build'
      ),
      $this->_filters($baseUri),
      '<h1>Build History</h1>',
      (new BuildRunsList($this->_buildRuns))->setHostController(
        $this->getHostController()
      ),
      new Impart(
        sprintf(
          '<div class="remodal" data-remodal-id="run-build-modal">
  <button data-remodal-action="close" class="remodal-close"></button>
   <div>Branch: %s </div>
  <button data-remodal-action="cancel" class="btn btn-danger">Cancel</button>
  <a href="%s" id="buildlink" class="btn btn-success ">Run Build</a>
  </div>',
          $selectbranch,
          $buildLink
        )
      )
    );
  }

  private function _getSelectBranch($branchAvailable, $buildLink)
  {
    $branches = '';
    foreach($branchAvailable as $branch)
    {
      $branches .= sprintf('<option value="%1$s">%1$s</option>', $branch);
    }

    return new Impart(
      sprintf(
        "<select
      onchange='
      document.getElementById(\"buildlink\").href=\"%s/\"+this.options[this.selectedIndex].value
      '>%s</select>",
        $buildLink,
        $branches
      )
    );
  }
}
