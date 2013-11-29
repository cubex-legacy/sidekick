<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository\Controllers;

use Sidekick\Applications\BaseApp\Controllers\BaseControl;

class RepositoryController extends BaseControl
{
  protected $_titlePrefix = 'Repository';

  public function getSidebar()
  {
    return null;
  }
}
