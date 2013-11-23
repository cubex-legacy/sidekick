<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli;

use Cubex\Cli\CliCommand;
use Sidekick\Components\Fortify\Mappers\BuildRun;

class Setup extends CliCommand
{
  public function execute()
  {
    \Log::info("Creating Build Run Table - Required for fortify homepage");
    (new BuildRun())->createTable();
  }
}
