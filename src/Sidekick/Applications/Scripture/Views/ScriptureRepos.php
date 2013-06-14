<?php
/**
 * @author: brooke.bryan
 *        Application: Scripture
 */
namespace Sidekick\Applications\Scripture\Views;

use Cubex\Data\Refine\Refinements\PropertyEqual;
use Cubex\Helpers\Strings;
use Cubex\View\HtmlElement;
use Cubex\View\Partial;
use Cubex\View\ViewModel;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Source;

class ScriptureRepos extends ViewModel
{
  /**
   * @var \Sidekick\Components\Projects\Mappers\Project[]
   */
  protected $_projecs;

  public function __construct($repositories, $currentProject = 0)
  {
    $this->_projecs     = assert_instances_of($repositories, new Project());
    $this->_currentRepo = $currentProject;
  }

  public function render()
  {
    $partial = new Partial('<li class="%s"><a href="%s">%s</a></li>');
    foreach($this->_projecs as $project)
    {
      $link = $this->baseUri() . '/' . $project->id() . '/README';
      $partial->addElement(
        ($this->_currentRepo == $project->id() ? 'active' : 'inactive'),
        $link,
        $project->name
      );
    }

    $tabs = new HtmlElement('div', ['class' => 'tabbable tabs-left']);
    $tabs->nestElement('ul', ['class' => 'nav nav-tabs'], $partial);
    return $tabs;
  }
}
