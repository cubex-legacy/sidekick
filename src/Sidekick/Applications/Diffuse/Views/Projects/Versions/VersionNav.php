<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Views\Projects\Versions;

use Cubex\Helpers\Strings;
use Cubex\View\HtmlElement;
use Cubex\View\Impart;
use Cubex\View\ViewModel;
use Sidekick\Components\Diffuse\Enums\VersionState;
use Sidekick\Components\Diffuse\Mappers\Version;

class VersionNav extends ViewModel
{
  protected $_platform = 0;
  protected $_platforms;
  protected $_version;

  public function __construct(
    Version $version, $currentPlatform = 0, array $platforms
  )
  {
    $this->_platform  = $currentPlatform;
    $this->_platforms = $platforms;
    $this->_version   = $version;
  }

  public function render()
  {
    $baseUri = '/diffuse/projects/v/' . $this->_version->id();

    $nav = new HtmlElement('div', ['class' => 'navbar-inner']);
    $nav->nestElement(
      'a',
      ['class' => 'brand', 'href' => $baseUri],
      ($this->_version->project()->name . ' ' . $this->_version->format())
    );

    $navItems = new HtmlElement('ul', ['class' => 'nav']);

    $navItems->nestElement(
      'li',
      $this->_platform === 0 ? ['class' => 'active'] : null,
      ('<a href="' . $baseUri . '">Details</a>')
    );

    foreach($this->_platforms as $id => $name)
    {
      $navItems->nestElement(
        'li',
        $this->_platform === $id ? ['class' => 'active'] : null,
        ('<a href="' . $baseUri . '/p/' . $id . '">' .
        Strings::titleize($name) . '</a>')
      );
    }

    $nav->nest($navItems);

    $status = Strings::humanize($this->_version->versionState);
    switch($this->_version->versionState)
    {
      case VersionState::APPROVED:
        $statusClass = 'success';
        break;
      case VersionState::REJECTED:
        $statusClass = 'error';
        break;
      case VersionState::REVIEW:
        $statusClass = 'warning';
        $status      = 'In Review';
        break;
      default:
        $statusClass = 'info';
        $status      = "Pending";
        break;
    }

    $nav->nest(
      new Impart(
        '<div class="pull-right"><span class="brand">Status: ' .
        '<span class="text-' . $statusClass . '">' . $status .
        '</span></span></div>'
      )
    );

    return new HtmlElement('div', ['class' => 'navbar'], $nav->render());
  }
}
