<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Components\Fortify;

interface IBuildTool
{
  public function setBuild(Build $build);

  public function run();
} 