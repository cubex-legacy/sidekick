<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 31/05/13
 * Time: 08:46
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Repository\Views;

use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Repository\Helpers\DiffusionHelper;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Source;

class RepositoryIndex extends TemplatedViewModel
{
  protected $_branches;

  public function __construct($branches)
  {
    $this->_branches = $branches;
  }

  /**
   * @return Branch[]
   */
  public function getBranches()
  {
    return $this->_branches;
  }
}
