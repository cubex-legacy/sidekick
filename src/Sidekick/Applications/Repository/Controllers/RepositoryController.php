<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository\Controllers;

use Sidekick\Applications\BaseApp\Controllers\ProjectAwareBaseControl;

class RepositoryController extends ProjectAwareBaseControl
{
  protected $_titlePrefix = 'Repository';

  public function getSidebar()
  {
    return null;
  }
}
