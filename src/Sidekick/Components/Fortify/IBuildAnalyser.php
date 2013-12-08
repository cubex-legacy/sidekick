<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Components\Fortify;

use Sidekick\Components\Fortify\Mappers\Build;

interface IBuildAnalyser
{
  public function setBuild(Build $build);

  public function analyse();
} 