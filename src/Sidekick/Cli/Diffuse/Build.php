<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;
use Cubex\Cli\Shell;
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

  public $verbose;

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

    $testsRun = $testsPass = $testsFailed = $testsCritical = 0;

    $buildRun->result = BuildResult::PASS;

    $commandList = $dependencies->getLoadOrder();
    foreach($commandList as $commandId)
    {
      $testsRun++;
      $command = new BuildCommand($commandId);

      echo "\n\n==========================================================\n";
      echo " Running " . $command->name;
      echo "\n==========================================================\n";

      $args = $returnVar = $output = null;
      if(is_array($command->args))
      {
        $args = ' ' . implode(" ", $command->args);
      }

      $run = $command->command . $args;

      $log = new BuildLog();
      if($this->verbose)
      {
        $log->enableOutput();
      }
      $log->setId($buildRun->id() . '-' . $command->id());
      $log->startTime = microtime(true);
      $log->exitCode  = -1;
      $log->saveChanges();

      chdir('../Cubex');

      $process = new Process($run);

      $process->run([$log, 'writeBuffer']);

      if($this->verbose)
      {
        echo "\n";
      }

      echo "\n$command->name Result: ";
      $returnValue = $process->getExitCode();
      if($returnValue === 0)
      {
        $testsPass++;
        echo Shell::colourText("PASS", Shell::COLOUR_FOREGROUND_GREEN);
      }
      else
      {
        $testsFailed++;
        if($command->causeBuildFailure)
        {
          $testsCritical++;
          $buildRun->result = BuildResult::FAIL;
        }
        echo Shell::colourText(
          "FAIL ($returnValue)",
          Shell::COLOUR_FOREGROUND_RED
        );
        Shell::colourText($run, Shell::COLOUR_FOREGROUND_LIGHT_BLUE);
      }

      $log->exitCode = (int)$process->getExitCode();
      $log->endTime  = microtime(true);
      $log->saveChanges();
    }

    $buildRun->endTime = time();
    $buildRun->saveChanges();

    echo "\n\n\n";
    echo Shell::colourText("Build Results", Shell::COLOUR_FOREGROUND_PURPLE);
    echo "\n";

    $results = [
      'Tests Run'      => $testsRun,
      'Tests Passed'   => $testsPass,
      'Tests Failed'   => $testsFailed,
      'Tests Critical' => $testsCritical
    ];

    foreach($results as $name => $value)
    {
      echo Shell::colourText(
        str_pad($name, 30, STR_PAD_RIGHT) . ' : ',
        Shell::COLOUR_FOREGROUND_WHITE
      );
      echo Shell::colourText($value, Shell::COLOUR_FOREGROUND_YELLOW);
      echo "\n";
    }

    echo "Final Result: ";

    if($buildRun->result !== BuildResult::PASS)
    {
      echo Shell::colourText("FAIL", Shell::COLOUR_FOREGROUND_RED);
    }
    else
    {
      echo Shell::colourText("PASS", Shell::COLOUR_FOREGROUND_GREEN);
    }

    echo "\n";
  }
}
