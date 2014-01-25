<?php
namespace Sidekick\Components\Fortify\Processes;

use Sidekick\Components\Fortify\FortifyBuildElement;

interface FortifyProcess extends FortifyBuildElement
{
  /**
   * @param $stage string Stage the process is running in e.g. Install
   *
   * @return bool|int Exit code, or true for success
   */
  public function process($stage);

  /**
   * Return the log from the process
   *
   * @return string
   */
  public function getLog();
}
