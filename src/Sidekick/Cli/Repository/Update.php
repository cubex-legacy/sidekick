<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Repository;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Cli\CliCommand;
use Cubex\Cli\PidFile;
use Cubex\Cli\Shell;
use Cubex\Facade\Queue;
use Cubex\Helpers\Strings;
use Cubex\I18n\TranslateTraits;
use Cubex\Log\Log;
use Cubex\Queue\StdQueue;
use Sidekick\Components\Repository\Mappers\Commit;
use Sidekick\Components\Repository\Mappers\CommitFile;
use Sidekick\Components\Repository\Mappers\Source;
use Symfony\Component\Process\Process;

/**
 * Repository Updater
 * @package Sidekick\Cli\Repository
 */
class Update extends CliCommand
{
  use TranslateTraits;

  /**
   * @required
   * @valuerequired
   */
  public $repositories;
  public $verbose;

  /**
   * @valuerequired
   */
  public $longInterval = 10;

  protected $_pidFile;

  protected $_currentRepoId;
  /**
   * @var Source
   */
  protected $_currentSource;

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
    if($this->repositories === 'all')
    {
      $repos = Source::collection()->get()->loadedIds();
    }
    else
    {
      $repos = Strings::stringToRange($this->repositories);
    }
    foreach($repos as $repoId)
    {
      $this->_currentRepoId = $repoId;
      $repo                 = new Source($this->_currentRepoId);
      $this->_currentSource = $repo;
      if(!$repo->exists())
      {
        Log::error("The repository specified could not be located");
        return;
      }

      echo Shell::colourText(
        "\nLoading Repository: " . $repo->name . "\n",
        Shell::COLOUR_FOREGROUND_GREEN
      );

      if(!file_exists($repo->localpath))
      {
        Log::info("Attempting to clone repo");
        $cloneCommand = 'git clone -v';
        $cloneCommand .= " $repo->fetchUrl";
        $cloneCommand .= " --branch " . $repo->branch;
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
        Log::error("The repo has not been checked out to: " . $repo->localpath);
        return;
      }

      chdir($repo->localpath);
      $process = new Process("git pull");
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

      Log::debug("Reading Commits");

      $this->_readCommits();
    }
    Log::info("Repository Update Complete");
    echo "\n";
  }

  protected function _readCommits()
  {
    $fromHash = '';
    try
    {
      $lastCommit = Commit::max(
        "committed_at",
        "%C = %d",
        "repository_id",
        $this->_currentRepoId
      );
    }
    catch(\Exception $e)
    {
      $lastCommit = '';
    }

    if($lastCommit)
    {
      $latest = Commit::collection(
        "%C = %d AND %C = %s",
        'repository_id',
        $this->_currentRepoId,
        'committed_at',
        $lastCommit
      )->setOrderBy("id", "DESC")->setLimit(0, 1)->first();
      /**
       * @var $latest Commit
       */

      if($latest)
      {
        $fromHash = "$latest->commitHash..";
      }
    }

    $format        = "%H%n%cn%n%ct%n%s%n%b%x03";
    $command       = "git log --format=\"$format\" --reverse $fromHash";
    $commitProcess = new Process($command);
    $commitProcess->run();
    $out         = $commitProcess->getOutput();
    $commits     = explode(chr(03), $out);
    $commitCount = 0;

    foreach($commits as $commit)
    {
      $commit = explode("\n", trim($commit), 5);
      if(count($commit) < 3)
      {
        continue;
      }
      $commit = array_pad($commit, 5, '');
      list($commitHash, $author, $date, $subject, $message) = $commit;

      $commitCount++;

      $commitHash = trim($commitHash);
      $author     = trim($author);
      $date       = trim($date);
      $subject    = trim($subject);
      $message    = trim($message);

      $commitO               = new Commit();
      $commitO->repositoryId = $this->_currentRepoId;
      $commitO->commitHash   = $commitHash;
      $commitO->author       = $author;
      $commitO->committedAt  = date("Y-m-d H:i:s", $date);
      $commitO->subject      = $subject;
      $commitO->message      = $message;
      $commitO->saveChanges();

      if($this->verbose)
      {
        Log::info('Adding ' . $commitHash . " - $subject");
      }

      $command = "git diff-tree --no-commit-id -r --name-status " . $commitHash;

      $diffProcess = new Process($command);
      $diffProcess->run();
      $changedFiles = explode("\n", $diffProcess->getOutput());
      foreach($changedFiles as $file)
      {
        if(stristr($file, "\t"))
        {
          list($changeType, $filePath) = explode("\t", $file, 2);
          $cFile               = new CommitFile();
          $cFile->changeType   = strtoupper(trim($changeType));
          $cFile->commitId     = $commitO->id();
          $cFile->repositoryId = $this->_currentRepoId;
          $cFile->filePath     = trim($filePath);
          $cFile->saveChanges();
        }
      }
    }

    Log::info(
      number_format($commitCount, 0) . $this->tp(
        " Commit(s) added",
        $commitCount
      )
    );

    if($commitCount > 0 && $this->_currentSource->commitBuildId > 0)
    {
      $queue = new StdQueue('buildRequest');
      Log::info(
        "Pushing to build queue " . $this->_currentSource->commitBuildId
      );
      Queue::push(
        $queue,
        [
        'respositoryId' => $this->_currentRepoId,
        'buildId'       => $this->_currentSource->commitBuildId,
        ]
      );
    }
  }
}
