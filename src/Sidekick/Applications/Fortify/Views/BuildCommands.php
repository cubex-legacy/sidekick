<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 06/06/13
 * Time: 14:25
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Components\Fortify\Mappers\Command;

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

  public function printDependencies($dependencies = [])
  {
    $output = '';
    if(is_array($dependencies))
    {
      $c = Command::collection()->loadIds($dependencies);

      $output = new RenderGroup(
        '<ul><li>',
        implode('</li><li>',$c->getUniqueField("name")),
        '</li></ul>'
      );
    }

    return $output;
  }
}
