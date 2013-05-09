<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;

class Codestyle extends CliCommand
{
  /**
   * @required
   * @valuerequired
   */
  public $build;

  public function execute()
  {
    echo file_get_contents('/builds/' . $this->build . '/logs/checkstyle.xml');
  }
}
