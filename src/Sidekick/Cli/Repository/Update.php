<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Repository;

use Cubex\Cli\CliCommand;
use Cubex\Cli\PidFile;
use Cubex\Facade\Queue;
use Cubex\Helpers\Strings;
use Cubex\I18n\TranslateTraits;
use Cubex\Log\Log;
use Cubex\Mapper\Database\RecordCollection;
use Cubex\Queue\StdQueue;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\CommitFile;
use Sidekick\Components\Repository\Mappers\Repository;
use Symfony\Component\Process\Process;

/**
 * Repository Updater
 * @package Sidekick\Cli\Repository
 */
class Update extends CliCommand
{
  use TranslateTraits;

  public $verbose;

  /**
   * @valuerequired
   */
  public $longInterval = 10;

  protected $_pidFile;

  protected $_currentBranchId;
  /**
   * @var Repository
   */
  protected $_currentRepository;
  /**
   * @var Branch
   */
  protected $_currentBranch;

  public function longRun()
  {
    $this->_pidFile = new PidFile();
    while(true)
    {
      $this->execute();
      sleep($this->longInterval);
    }
  }

  public function execute()
  {
    $repos = Repository::collection()->get();
    foreach($repos as $repo)
    {
      $this->_currentRepository = $repo;
      Log::info("Loading Repository: " . $repo->name);

      if(!file_exists($repo->localpath))
      {
        Log::info("Attempting to clone repo");
        $cloneCommand = 'git clone -v';
        $cloneCommand .= " $repo->fetchUrl";
        $cloneCommand .= " " . $repo->localpath;
        Log::debug($cloneCommand);

        $process = new Process($cloneCommand);
        $process->run();
        if($this->verbose)
        {
          echo $process->getOutput();
        }
      }

      if(!file_exists($repo->localpath))
      {
        Log::error(
          "The repo has not been checked out to: " . $repo->localpath
        );
        continue;
      }

      $process = new Process("git pull", $repo->localpath);
      $process->run(
        function ($type, $data)
        {
          echo $data;
        }
      );

      if($this->verbose)
      {
        echo $process->getOutput();
      }

      Log::info("Repository up to date.");

      Log::debug("Updating Branches");

      //get all branches
      $this->_updateBranches($repo);

    }
    Log::info("Repository Update Complete");
  }

  private function _updateBranches($repo)
  {
    $process = new Process("git branch -a", $repo->localpath);
    $process->run();
    $output = explode("\n", $process->getOutput());
    foreach($output as $line)
    {
      if($line && strpos($line, 'remotes/origin/HEAD') === false)
      {
        $line   = trim(str_replace('*', '', $line));
        $branch = basename($line);
        $existingBranch = Branch::collection()->loadWhere(
          ['name' => $branch, 'repositoryId' => $repo->id()]
        );
        if($existingBranch->count() == 0)
        {
          Log::debug("New Branch found: $branch");
          $b               = new Branch();
          $b->repositoryId = $repo->id();
          $b->name         = $branch;
          $b->branch       = $branch;
          $b->saveChanges();
        }
      }
    }
  }
}
