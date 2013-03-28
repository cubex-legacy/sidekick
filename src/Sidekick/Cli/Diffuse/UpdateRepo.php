<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Bundl\Debugger\DebuggerBundle;
use Cubex\Cli\CliCommand;
use Sidekick\Components\Diffuse\Mappers\Commit;

class UpdateRepo extends CliCommand
{
  public function execute()
  {
    $repositoryId = 2;
    $x            = new DebuggerBundle();
    //$x->init();

    $fromHash   = '';
    $lastCommit = Commit::max(
      "committed_at",
      "%C = %d",
      "repository_id",
      $repositoryId
    );
    if($lastCommit)
    {
      $latest = Commit::loadWhereOrNew(
        "%C = %d AND %C = %s",
        'repository_id',
        $repositoryId,
        'committed_at',
        $lastCommit
      );

      if($latest)
      {
        $fromHash = "$latest->commitHash..";
      }
    }

    chdir('../Cubex');
    $format  = "%H%n%cn%n%ct%n%s%n%b%x03";
    $out     = `git log --format="$format" --reverse $fromHash`;
    $commits = explode(chr(03), $out);

    foreach($commits as $commit)
    {
      $commit = explode("\n", trim($commit), 5);
      if(count($commit) < 3)
      {
        continue;
      }
      list($commitHash, $author, $date, $subject, $message) = $commit;

      $commitHash = trim($commitHash);
      $author     = trim($author);
      $date       = trim($date);
      $subject    = trim($subject);
      $message    = trim($message);

      $commitO               = new Commit();
      $commitO->repositoryId = $repositoryId;
      $commitO->commitHash   = $commitHash;
      $commitO->author       = $author;
      $commitO->committedAt  = date("Y-m-d H:i:s", $date);
      $commitO->subject      = $subject;
      $commitO->message      = $message;
      $commitO->saveChanges();

      echo 'Adding ' . $commitHash . "\n";
    }
  }
}
