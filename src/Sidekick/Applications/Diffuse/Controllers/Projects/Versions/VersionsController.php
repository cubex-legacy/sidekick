<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Versions;

use Cubex\Foundation\IRenderable;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\View\RenderGroup;
use Cubex\View\ViewModel;
use Sidekick\Applications\Diffuse\Controllers\DiffuseController;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionNav;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\PlatformVersionState;
use Sidekick\Components\Diffuse\Mappers\Version;

class VersionsController extends DiffuseController
{
  /**
   * @var Version
   */
  protected $_version;
  /**
   * @var Platform[]|RecordCollection
   */
  protected $_platforms;
  /**
   * @var PlatformVersionState[]
   */
  protected $_platformStates;

  public function preProcess()
  {
    parent::preProcess();
    $this->_version        = new Version($this->getInt("versionId"));
    $this->_platforms      = Platform::orderedCollection();
    $this->_platformStates = PlatformVersionState::collection(
      ['version_id' => $this->_version->id()]
    );
  }

  protected function _buildView(IRenderable $view)
  {
    if($view instanceof ViewModel)
    {
      $view = $this->createView($view);
    }

    return new RenderGroup(
      $this->createView(
        new VersionNav(
          $this->_version, $this->getInt("platformId", 0),
          $this->_platforms->getKeyPair("id", "name")
        )
      ),
      $view
    );
  }
}
