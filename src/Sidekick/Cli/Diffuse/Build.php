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
use Cubex\Helpers\System;
use Sidekick\Components\Diffuse\Enums\BuildResult;
use Sidekick\Components\Diffuse\Mappers\BuildCommand;
use Sidekick\Components\Diffuse\Mappers\BuildLog;
use Sidekick\Components\Diffuse\Mappers\BuildRun;
use Sidekick\Components\Diffuse\Mappers\BuildsCommands;
use Sidekick\Components\Diffuse\Mappers\BuildsProjects;
use Sidekick\Components\Diffuse\Mappers\Patch;
use Sidekick\Components\Projects\Mappers\Project;
use Sidekick\Components\Repository\Enums\RepositoryProvider;
use Sidekick\Components\Repository\Mappers\Source;
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

  /**
   * The Patch ID you wish to build with
   * @valuerequired
   * @example 1
   */
  public $patch;

  public $verbose;

  /**
   * Number of seconds before a single process will timeout
   * @valuerequired
   */
  public $timeout = 120;

  protected $_buildId;
  protected $_buildResult;
  protected $_buildSourceDir;
  protected $_buildPath;

  protected $_totalTests = 0;
  protected $_testsRun = 0;
  protected $_testsPass = 0;
  protected $_testsFail = 0;
  protected $_lineSplit = '';

  public function execute()
  {
    $this->_lineSplit = str_repeat('=', 80);

    $projectId = (int)$this->project;
    $buildId   = (int)$this->build;

    $project      = new Project($projectId);
    $build        = new \Sidekick\Components\Diffuse\Mappers\Build($buildId);
    $buildProject = new BuildsProjects($build, $project);

    $buildRun            = new BuildRun();
    $buildRun->buildId   = $build->id();
    $buildRun->projectId = $project->id();
    $buildRun->startTime = new \DateTime();
    $buildRun->commands  = [];
    $this->_buildResult  = $buildRun->result = BuildResult::RUNNING;
    $buildRun->saveChanges();
    $this->_buildId = $buildRun->id();

    if(!System::isWindows())
    {
      declare(ticks = 1);
      pcntl_signal(SIGINT, array($buildRun, "exited"));
      pcntl_signal(SIGTERM, array($buildRun, "exited"));
      pcntl_signal(SIGHUP, array($buildRun, "exited"));
      pcntl_signal(SIGUSR1, array($buildRun, "exited"));
      pcntl_signal(SIGKILL, array($buildRun, "exited"));
    }

    echo Shell::colourText(
      "\n" .
      "Starting Build for: " . $project->name . " (" . $build->name . ")\n" .
      "Build ID: " . $this->_buildId .
      "\n",
      Shell::COLOUR_FOREGROUND_LIGHT_BLUE
    );

    $buildPath = '../builds/' . $buildRun->id();
    mkdir($buildPath, 0777, true);
    chdir($buildPath);
    $buildPath             = getcwd();
    $this->_buildPath      = $buildPath;
    $this->_buildSourceDir = $build->sourceDirectory;

    $buildRun->commands = array_merge($buildRun->commands, ['source']);
    $buildRun->endTime  = new \DateTime();
    $buildRun->saveChanges();

    $buildSource = new Source($buildProject->buildSourceId);
    $this->_downloadSourceCode($buildSource, $this->_buildSourceDir);

    echo "Getting Build Repo Hash\n";
    chdir($this->_buildSourceDir);
    $process = new Process("git rev-parse --verify HEAD");
    $process->setTimeout($this->timeout);
    $process->run();
    $buildRun->commitHash = $process->getOutput();
    chdir($buildPath);

    if($this->patch !== null)
    {
      $this->_applyPatch($this->patch, $this->_buildSourceDir);
    }

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
      $this->_totalTests++;
    }

    $commandList = $dependencies->getLoadOrder();
    foreach($commandList as $commandId)
    {
      $buildRun->commands = array_merge($buildRun->commands, [$commandId]);
      $buildRun->endTime  = new \DateTime();
      $buildRun->saveChanges();
      $pass = $this->_runCommand($commandId);
      if(!$pass)
      {
        break;
      }
    }

    if($this->_buildResult === BuildResult::RUNNING)
    {
      $this->_buildResult = BuildResult::PASS;
    }

    $buildRun->result  = $this->_buildResult;
    $buildRun->endTime = new \DateTime();
    $buildRun->saveChanges();

    $this->_buildResults($buildRun);
  }

  protected function _runCommand($commandId)
  {
    $lineSplitter = $this->_lineSplit;
    $this->_testsRun++;
    $command = new BuildCommand($commandId);

    echo "\n\n$lineSplitter\n";
    echo " Running " . $command->name;
    echo "\n$lineSplitter\n";

    $log = new BuildLog();
    if($this->verbose)
    {
      $log->enableOutput();
    }
    $log->setId($this->_buildId . '-' . $command->id());
    $log->startTime = microtime(true);
    $log->exitCode  = -1;
    $log->saveChanges();

    $exitCode = $this->_processCommand($log, $command);

    echo "\n$command->name Result: ";

    $returnValue   = $exitCode;
    $log->exitCode = (int)$returnValue;
    $log->endTime  = microtime(true);
    $log->saveChanges();

    if($returnValue === 0)
    {
      $this->_testsPass++;
      echo Shell::colourText("PASS", Shell::COLOUR_FOREGROUND_GREEN);
    }
    else
    {
      echo Shell::colourText(
        "FAIL ($returnValue)",
        Shell::COLOUR_FOREGROUND_RED
      );

      $this->_testsFail++;

      if($command->causeBuildFailure)
      {
        $this->_buildResult = BuildResult::FAIL;
        return false;
      }
    }
    return true;
  }

  protected function _processCommand(BuildLog $log, BuildCommand $command)
  {
    $args = null;
    if(is_array($command->args))
    {
      $args = ' ' . implode(" ", $command->args);
    }

    $runCommand = $command->command . $args;

    $runCommand = str_replace(
      '{sourcedirectory}',
      $this->_buildSourceDir,
      $runCommand
    );

    if($command->runOnFileSet)
    {
      $command->fileSetDirectory = str_replace(
        '{sourcedirectory}',
        $this->_buildSourceDir,
        $command->fileSetDirectory
      );
      $returnExitCode            = -1;
      $files                     = $this->_getFullFilelisting(
        $command->fileSetDirectory,
        $command->filePattern
      );
      foreach($files as $file)
      {
        if(stristr($runCommand, '{iteratedFilePath}'))
        {
          $process = new Process(
            str_replace(
              '{iteratedFilePath}',
              $file,
              $runCommand
            )
          );
        }
        else
        {
          $process = new Process($runCommand . ' ' . $file);
        }

        $process->setTimeout($this->timeout);
        $process->run([$log, 'writeBuffer']);
        $exitCode = $process->getExitCode();
        if($exitCode > $returnExitCode)
        {
          $returnExitCode = $exitCode;
        }
      }
      return $returnExitCode;
    }
    else
    {
      $process = new Process($runCommand);
      $process->setTimeout($this->timeout);
      echo "\nRunning: " . $runCommand . "\n";
      $process->run([$log, 'writeBuffer']);
      return $process->getExitCode();
    }
  }

  protected function _getFullFilelisting($directory, $filePatten = '.*')
  {
    $files = [];

    $recursive = new \RecursiveDirectoryIterator($directory);
    foreach($recursive as $file)
    {
      /***
       * @var $file \SplFileInfo
       */
      $filePath = $file->getRealPath();
      if($file->isDir() && !in_array($file->getFilename(), ['.', '..']))
      {
        $files = array_merge(
          $files,
          $this->_getFullFilelisting($filePath, $filePatten)
        );
      }
      else if(preg_match("/$filePatten/", $filePath))
      {
        $files[] = $filePath;
      }
    }

    return $files;
  }

  protected function _buildResults(BuildRun $buildRun)
  {
    $lineSplitter = $this->_lineSplit;
    echo "\n\n\n$lineSplitter\n";

    echo Shell::colourText(
      "         Build Results",
      Shell::COLOUR_FOREGROUND_LIGHT_PURPLE
    );
    echo "\n$lineSplitter\n\n";

    $results = [
      'Build ID'       => $this->_buildId,
      null,
      'Tests Run'      => $this->_testsRun . '/' . $this->_totalTests,
      'Tests Passed'   => $this->_testsPass,
      'Tests Failed'   => $this->_testsFail,
      null,
      'Start Time'     => $buildRun->startTime->format('Y-m-d H:i:s'),
      'End Time'       => $buildRun->endTime->format('Y-m-d H:i:s'),
      'Total Duration' => $buildRun->startTime->diff($buildRun->endTime)
      ->format("%H:%I:%S"),
    ];

    foreach($results as $name => $value)
    {
      if($value !== null)
      {
        echo " " . str_pad($name, 20, ' ', STR_PAD_RIGHT) . ' : ';
        echo Shell::colourText($value, Shell::COLOUR_FOREGROUND_YELLOW);
      }
      echo "\n";
    }

    echo "\n$lineSplitter\n";
    echo "Final Result: ";

    if($buildRun->result !== BuildResult::PASS)
    {
      echo Shell::colourText("FAIL", Shell::COLOUR_FOREGROUND_RED);
    }
    else
    {
      echo Shell::colourText("PASS", Shell::COLOUR_FOREGROUND_GREEN);
    }

    echo "\n$lineSplitter\n\n";
  }

  protected function _downloadSourceCode(Source $source, $location)
  {
    $log = new BuildLog();
    if($this->verbose)
    {
      $log->enableOutput();
    }
    $log->setId($this->_buildId . '-source');
    $log->startTime = microtime(true);
    $log->exitCode  = -1;
    $log->saveChanges();

    switch($source->repositoryType)
    {
      case RepositoryProvider::GIT:
        $this->_outputStep("Cloning Repo");

        $cloneCommand = 'git clone -v';
        $cloneCommand .= " $source->fetchUrl";
        $cloneCommand .= " --branch " . $source->branch;
        $cloneCommand .= " $location";

        $process = new Process($cloneCommand);
        $process->setTimeout($this->timeout);
        $process->run([$log, 'writeBuffer']);
        $log->exitCode = $process->getExitCode();
    }

    $log->endTime = microtime(true);
    $log->saveChanges();

    return $log->exitCode;
  }

  protected function _applyPatch($patchId)
  {
    $log = new BuildLog();
    if($this->verbose)
    {
      $log->enableOutput();
    }
    $log->setId($this->_buildId . '-patch');
    $log->startTime = microtime(true);
    $log->exitCode  = -1;
    $log->saveChanges();

    $patch = new Patch($patchId);
    if($patch->exists())
    {
      $this->_outputStep("Applying Patch");

      $patchPath = $this->_buildPath . DS . $patch->filename;
      file_put_contents($patchPath, $patch->patch);

      $cwd = getcwd();
      chdir($this->_buildSourceDir);
      $runCommand = "git apply -v " . $patchPath . ' -p' . $patch->leadingSlashes;
      $process    = new Process($runCommand);
      $process->setTimeout($this->timeout);
      $process->run([$log, 'writeBuffer']);
      $log->exitCode = $process->getExitCode();
      chdir($cwd);
    }

    $log->endTime = microtime(true);
    $log->saveChanges();

    return $log->exitCode;
  }

  protected function _outputStep($message)
  {
    echo Shell::colourText("\n  $message\n", Shell::COLOUR_FOREGROUND_CYAN);
  }
}
