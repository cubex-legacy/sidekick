<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Components\Fortify;

interface IFileAnalyser extends IBuildAnalyser
{
  public function setFilePath($path);
} 