<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 06/06/13
 * Time: 14:25
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\View\Partial;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\BaseApp\Views\Alert;
use Sidekick\Components\Fortify\Mappers\Command;
use Sidekick\Components\Helpers\BuildCommandsHelper;

class BuildCommands extends TemplatedViewModel
{
  protected $_buildCommands;
  protected $_alert;

  public function __construct($buildCommands)
  {
    $this->_buildCommands = $buildCommands;
    try
    {
      $this->_orderByDependencies();
    }
    catch(\Exception $e)
    {
      $this->_alert = new Alert(
        Alert::TYPE_ERROR,
        'You have some impossible set of dependencies'
      );
    }
  }

  public function getBuildCommands()
  {
    return $this->_buildCommands;
  }

  public function getAlert()
  {
    return $this->_alert;
  }

  public function printDependencies($dependencies = [])
  {
    $output = '';
    if(is_array($dependencies))
    {
      $c      = Command::collection()->loadIds($dependencies);
      $list   = new Partial('<li>%s</li>');
      $output = new RenderGroup(
        '<ul>',
        $list->addElements($c->getUniqueField("name")),
        '</ul>'
      );
    }

    return $output;
  }

  private function _orderByDependencies()
  {
    $this->_buildCommands = BuildCommandsHelper::orderByDependencies(
      $this->getBuildCommands()
    );
  }
}
