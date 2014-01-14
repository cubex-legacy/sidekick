<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Analysers;

use Sidekick\Components\Fortify\FortifyBuildElement;
use Sidekick\Components\Repository\Mappers\Commit;

interface FortifyAnalyser extends FortifyBuildElement
{
  /**
   * @param Commit $commit
   *
   * @return bool Completed Analysis
   */
  public function analyse(Commit $commit);
}
