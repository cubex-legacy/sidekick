<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Controllers\Projects\Versions;

use Cubex\View\RenderGroup;
use Sidekick\Applications\Diffuse\Views\Projects\Versions\VersionDetailsView;
use Sidekick\Components\Repository\Mappers\Commit;

class VersionDetailController extends VersionsController
{
  public function renderIndex()
  {
    $view = new VersionDetailsView(
      $this->_version, $this->_platforms, $this->_platformStates
    );

    if($this->_version->fromCommitHash !== null && $this->_version->toCommitHash !== null)
    {
      $view->setCommits(
        Commit::collectionBetween(
          $this->_version->fromCommitHash,
          $this->_version->toCommitHash
        )
      );
    }

    return $this->_buildView($view);
  }
}
