<?php
namespace Sidekick\Components\Fortify;

use Sidekick\Components\Fortify\Mappers\CommitBuildInsight;
use Sidekick\Components\Repository\Mappers\Branch;

interface FortifyBuildElement
{
  public function setInsight(CommitBuildInsight $insight);

  public function setBranch(Branch $branch);

  public function setAlias($alias);

  public function setStage($stage);

  public function setScratchDir($scratchDir);

  public function configure($configuration);

  public function setRepoBasePath($basePath);
}
