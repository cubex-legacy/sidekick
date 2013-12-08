<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Components\Fortify;

interface IDirectoryAnalyser extends IBuildAnalyser
{
  public function setDirectory($path);
}