<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Repository;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
use Cubex\Helpers\Strings;
use Cubex\I18n\TranslateTraits;
use Sidekick\Components\Repository\Mappers\Commit;
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

  protected $_currentRepoId;

  public function execute()
  {
    $repos = Strings::stringToRange($this->repositories);
    foreach($repos as $repoId)
    {
      $this->_currentRepoId = $repoId;
      $repo                 = new Source($this->_currentRepoId);
      if(!$repo->exists())
      {
        echo "The repository specified could not be located\n";
        return;
      }

      echo Shell::colourText(
        "\nLoading Repository: " . $repo->name . "\n",
        Shell::COLOUR_FOREGROUND_GREEN
      );

      if(!file_exists($repo->localpath))
      {
        echo "Attempting to clone repo\n";
        $cloneCommand = 'git clone -v';
        $cloneCommand .= " $repo->fetchUrl";
        $cloneCommand .= " --branch " . $repo->branch;
        $cloneCommand .= " " . $repo->localpath;
        $process = new Process($cloneCommand);
        $process->run();
      }

      if(!file_exists($repo->localpath))
      {
        echo "The repo has not been checked out to: " . $repo->localpath . "\n";
        return;
      }

      chdir($repo->localpath);
      $process = new Process("git pull");
      $process->run(function ($type, $data) { echo $data; });

      echo "Repository up to date.\n";

      echo "Reading Commits\n";

      $this->_readCommits();
    }
    echo "\nRepository Update Complete\n";
  }

  protected function _readCommits()
  {
    $fromHash = '';
    try
    {
      $x         = new Commit(1);
      $x->author = 'Sample';
      $x->saveChanges();

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
      $latest = Commit::loadWhereOrNew(
        "%C = %d AND %C = %s",
        'repository_id',
        $this->_currentRepoId,
        'committed_at',
        $lastCommit
      );

      if($latest)
      {
        $fromHash = "$latest->commitHash..";
      }
    }

    $format      = "%H%n%cn%n%ct%n%s%n%b%x03";
    $out         = `git log --format="$format" --reverse $fromHash`;
    $commits     = explode(chr(03), $out);
    $commitCount = 0;

    foreach($commits as $commit)
    {
      $commit = explode("\n", trim($commit), 5);
      if(count($commit) < 3)
      {
        continue;
      }
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
        echo 'Adding ' . $commitHash . " - $subject\n";
      }
    }

    echo number_format($commitCount, 0) . $this->tp(
      " Commit(s) added\n",
      $commitCount
    );
  }
}
