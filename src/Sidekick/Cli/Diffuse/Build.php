<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;
use Cubex\FileSystem\FileSystem;
use Cubex\Helpers\DependencyArray;
use Cubex\Helpers\Strings;
use Sidekick\Components\Diffuse\Enums\BuildResult;
use Sidekick\Components\Diffuse\Mappers\BuildCommand;
use Sidekick\Components\Diffuse\Mappers\BuildLog;
use Sidekick\Components\Diffuse\Mappers\BuildRun;
use Sidekick\Components\Diffuse\Mappers\BuildsCommands;
use Sidekick\Components\Projects\Mappers\Project;
use Symfony\Component\Process\Process;

class Build extends CliCommand
{
  /**
   * The Project ID you wish to run a build for
   * @required
   * @valuerequired
   * @example PIDX
   */
  public $project;

  /**
   * The Build ID you wish to run a build for
   * @required
   * @valuerequired
   * @example BIDX
   */
  public $build;

  public function execute()
  {
    $projectId = (int)$this->project;
    $buildId   = (int)$this->build;

    $project = new Project($projectId);
    $build   = new \Sidekick\Components\Diffuse\Mappers\Build($buildId);

    echo "\n";
    echo "Starting Build for: " . $project->name . " (" . $build->name . ")";

    $buildRun            = new BuildRun();
    $buildRun->buildId   = $build->id();
    $buildRun->projectId = $project->id();
    $buildRun->startTime = time();
    $buildRun->result    = BuildResult::RUNNING;
    $buildRun->saveChanges();

    $commands     = BuildsCommands::collectionOn($build);
    $dependencies = new DependencyArray();

    foreach($commands as $com)
    {
      /**
       * @var $com BuildsCommands
       */
      $dependencies->add(
        $com->getData("buildcommand_id"),
        $com->getData("dependencies")
      );
    }

    $buildRun->result = BuildResult::PASS;

    $commandList = $dependencies->getLoadOrder();
    foreach($commandList as $commandId)
    {
      echo "\n\n==========================================================\n\n";
      $command = new BuildCommand($commandId);
      $args    = $returnVar = $output = null;
      if(is_array($command->args))
      {
        $args = ' ' . implode(" ", $command->args);
      }

      $run = $command->command . $args;

      $log = new BuildLog();
      $log->setId($buildRun->id() . '-' . $command->id());
      $log->startTime = microtime(true);

      chdir('../Cubex');
      $process = new Process($run);
      $process->run([$log, 'writeBuffer']);

      $returnValue = $process->getExitCode();
      if($returnValue === 0)
      {
        echo "Passed Test: $command->name\n";
      }
      else
      {
        $buildRun->result = BuildResult::FAIL;
        echo "FAILED Test: $command->name with code $returnValue\n";
      }

      $log->exitCode = (int)$process->getExitCode();
      $log->endTime  = microtime(true);
      $log->saveChanges();
    }

    $buildRun->endTime = time();
    $buildRun->saveChanges();
  }
}
