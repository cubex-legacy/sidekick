<?php
/**
 * Author: oke.ugwu
 * Date: 28/06/13 17:51
 */

namespace Sidekick\Components\Helpers;

use Cubex\Helpers\DependencyArray;

class BuildCommandsHelper
{
  /**
   * Orders a collection of commands by their dependency.
   * So that less dependent commands come first
   *
   * @param \Sidekick\Components\Fortify\Mappers\BuildsCommands[] $commands
   *
   * @return \Sidekick\Components\Fortify\Mappers\BuildsCommands[]
   */
  public static function orderByDependencies($commands)
  {
    $dependencies = new DependencyArray();
    $rebuild      = []; //lookup array to be used to rebuild list
    foreach($commands as $com)
    {
      $dependencies->add(
        $com->commandId,
        $com->dependencies
      );

      $rebuild[$com->commandId] = $com;
    }

    $orderedCommandList = $dependencies->getLoadOrder();
    $orderedList        = [];
    foreach($orderedCommandList as $commandId)
    {
      $orderedList[] = $rebuild[$commandId];
    }

    return $orderedList;
  }
}
