<?php
namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;
use Sidekick\Components\Fortify\Mappers\CommitBuild;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;

class CommitRebuild extends CliCommand
{
  /**
   * @valuerequired
   * @required
   */
  public $branch;

  public function execute()
  {
    $branch = new Branch($this->branch);
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
