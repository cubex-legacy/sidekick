<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;

class BuildProject extends CliCommand
{
  public function execute()
  {
    chdir('../Cubex');
    $returnVar = $output = null;
    exec("ant", $output, $returnVar);

    var_dump($output);
    echo "\n\nReturn Variable: ";
    var_dump($returnVar);
  }
}
