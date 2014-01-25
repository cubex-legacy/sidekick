<?php
namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;
use Sidekick\Components\Fortify\Mappers\CommitBuild;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;

class CommitRebuild extends CliCommand
{
  public function execute()
  {
    $branch = new Branch(1);
    foreach($branch->commits() as $commit)
    {
      /**
       * @var $commit Commit
       */
      $commitBuild           = new CommitBuild();
      $commitBuild->commit   = $commit->commitHash;
      $commitBuild->branchId = $branch->id;
      $commitBuild->saveChanges();
    }
  }
}
