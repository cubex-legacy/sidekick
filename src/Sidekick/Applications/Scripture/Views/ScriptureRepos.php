<?php
/**
 * @author: brooke.bryan
 *        Application: Scripture
 */
namespace Sidekick\Applications\Scripture\Views;

use Cubex\Helpers\Strings;
use Cubex\View\HtmlElement;
use Cubex\View\Partial;
use Cubex\View\ViewModel;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Mappers\Source;

class ScriptureRepos extends ViewModel
{
  /**
   * @return \Sidekick\Components\Projects\Mappers\Project[]
   */
  protected $_repositories;

  public function __construct($repositories, $currentRepo = 0)
  {
    $this->_repositories = assert_instances_of($repositories, new Project());
    $this->_currentRepo  = $currentRepo;
  }

  public function render()
  {
    $partial = new Partial('<li class="%s"><a href="%s">%s</a></li>');
    foreach($this->_repositories as $project)
    {
      /**
       * @var $project Project
       * @var $source  Source
       */
      $source = $project->repository()->first();
      if($source !== null)
      {
        $link = $this->baseUri() . '/' . $source->id() . '/README';
        $partial->addElement(
          ($this->_currentRepo == $source->id() ? 'active' : 'inactive'),
          $link,
          $source->name
        );
      }
    }

    $tabs = new HtmlElement('div', ['class' => 'tabbable tabs-left']);
    $tabs->nestElement('ul', ['class' => 'nav nav-tabs'], $partial);
    return $tabs;
  }
}
