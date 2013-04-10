<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Repository\Components\Repositories;

class Git implements Repository
{
  public function lastCommit()
  {
    $command = 'git log -1 --pretty=format:%H';

    return $this->_runCommand($command);
  }

  protected function _runCommand($command)
  {
    return $command;
  }
}
