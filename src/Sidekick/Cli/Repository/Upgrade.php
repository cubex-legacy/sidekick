<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Cli\Repository;

use Cubex\Cli\CliCommand;
use Cubex\Database\ConnectionMode;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\Repository;
use Sidekick\Components\Repository\Mappers\Source;

/**
 * Migrate from source to repository / branch mappers
 */
class Upgrade extends CliCommand
{
  public function execute()
  {

    $table = Commit::tableName();
    try

    {
      Commit::conn(ConnectionMode::WRITE())->query(
        "ALTER TABLE  `$table` ADD INDEX `branch_id` (  `branch_id` )"
      );
    }
    catch(\Exception $e)
    {
    }

    try
    {
      Commit::conn(ConnectionMode::WRITE())->query(
        "ALTER TABLE  `$table` ADD UNIQUE `commit_branch` (  `commit_hash`,`branch_id` )"
      );
    }
    catch(\Exception $e)
    {
    }

    $sources = Source::collection();
    foreach($sources as $source)
    {
      /**
       * @var $source Source
       */
      $repo = Repository::loadWhereOrNew(["projectId" => $source->projectId]);
      /**
       * @var $repo Repository
       */
      $repo->projectId      = $source->projectId;
      $repo->fetchUrl       = $source->fetchUrl;
      $repo->localpath      = $source->localpath;
      $repo->name           = $source->name;
      $repo->description    = $source->description;
      $repo->username       = $source->username;
      $repo->password       = $source->password;
      $repo->repositoryType = $source->repositoryType;
      $repo->saveChanges();

      $branch = Branch::loadWhereOrNew(
        ["repository_id" => $repo->id(), "branch" => $source->branch]
      );
      /**
       * @var $branch Branch
       */
      $branch->branch        = $source->branch;
      $branch->name          = $source->branch . ' branch';
      $branch->repositoryId  = $repo->id();
      $branch->commitBuildId = $source->commitBuildId;
      $branch->saveChanges();

      $commits = Commit::collection(["repository_id" => $source->id()]);
      foreach($commits as $commit)
      {
        $commit->branchId = $branch->id();
        $commit->saveChanges();
      }
    }
    //DROP commitFiles repositoryId
  }
} 