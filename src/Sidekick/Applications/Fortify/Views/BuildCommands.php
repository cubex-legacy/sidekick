<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 06/06/13
 * Time: 14:25
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\TemplatedViewModel;

class BuildCommands extends TemplatedViewModel
{
  protected $_buildCommands;

  public function __construct($buildCommands)
  {
    $this->_buildCommands = $buildCommands;
  }

  public function getBuildCommands()
  {
    return $this->_buildCommands;
  }
}
