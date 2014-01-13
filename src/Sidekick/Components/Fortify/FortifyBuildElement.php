<?php
namespace Sidekick\Components\Fortify;

use Sidekick\Components\Repository\Mappers\Branch;

interface FortifyBuildElement
{
  public function setBranch(Branch $branch);

  public function setScratchDir($scratchDir);

  public function configure($configuration);

  public function setRepoBasePath($basePath);
}
