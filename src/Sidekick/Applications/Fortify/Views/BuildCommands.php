<?php
/**
 * Created by JetBrains PhpStorm.
 * User: oke.ugwu
 * Date: 06/06/13
 * Time: 14:25
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Applications\Fortify\Views;

use Cubex\Helpers\DependencyArray;
use Cubex\View\RenderGroup;
use Cubex\View\TemplatedViewModel;
use Sidekick\Applications\BaseApp\Views\Alert;
use Sidekick\Components\Fortify\Mappers\Command;

class BuildCommands extends TemplatedViewModel
{
  protected $_buildCommands;
  protected $_alert;

  public function __construct($buildCommands)
  {
    $this->_buildCommands = $buildCommands;
    try
    {
      $this->orderByDependencies();
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
      $c = Command::collection()->loadIds($dependencies);

      $output = new RenderGroup(
        '<ul><li>',
        implode('</li><li>', $c->getUniqueField("name")),
        '</li></ul>'
      );
    }

    return $output;
  }


  public function orderByDependencies()
  {
    $dependencies = new DependencyArray();
    $rebuild      = []; //lookup array to be used to rebuild $_buildCommands
    foreach($this->getBuildCommands() as $com)
    {
      /**
       * @var $com BuildsCommands
       */
      $dependencies->add(
        $com->commandId,
        $com->dependencies
      );

      $rebuild[$com->commandId] = $com;
    }

    $orderedCommandList   = $dependencies->getLoadOrder();
    $this->_buildCommands = []; //reset to rebuild
    foreach($orderedCommandList as $commandId)
    {
      $this->_buildCommands[] = $rebuild[$commandId];
    }
  }
}
