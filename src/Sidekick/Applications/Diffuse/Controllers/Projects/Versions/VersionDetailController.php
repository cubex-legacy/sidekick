<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Versions;

use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionDetailsView;

class VersionDetailController extends VersionsController
{
  public function renderIndex()
  {
    return $this->_buildView(
      new VersionDetailsView(
        $this->_version, $this->_platforms, $this->_platformStates
      )
    );
  }
}
