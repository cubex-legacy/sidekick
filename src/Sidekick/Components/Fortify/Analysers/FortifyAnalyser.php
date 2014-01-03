<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Analysers;

interface FortifyAnalyser
{
  public function configure($configuration);

  public function setFileBasePath($basePath);

  public function addFile($filePath);

  /**
   * @return bool
   */
  public function analyse();
}
