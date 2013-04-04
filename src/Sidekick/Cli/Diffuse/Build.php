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

  protected $_buildId;
  protected $_buildResult;

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
    $this->_buildId = $buildRun->id();

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

    $this->_buildResult = BuildResult::PASS;

    $commandList = $dependencies->getLoadOrder();
    foreach($commandList as $commandId)
    {
      $pass = $this->runCommand($commandId);
      if(!$pass)
      {
        break;
      }
    }

    $buildRun->result  = $this->_buildResult;
    $buildRun->endTime = time();
    $buildRun->saveChanges();

    $this->_buildResults($buildRun);
  }

  protected function runCommand($commandId)
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

    chdir('../Cubex');

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

      if($command->causeBuildFailure)
      {
        $this->_testsFail++;
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

    if($command->runOnFileSet)
    {
      $returnExitCode = -1;
      $files          = $this->_getFullFilelisting(
        $command->fileSetDirectory,
        $command->filePattern
      );
      foreach($files as $file)
      {
        if(stristr($runCommand, '{iteratedFilePath}'))
        {
          $process = new Process(str_replace(
            '{iteratedFilePath}',
            $file,
            $runCommand
          ));
        }
        else
        {
          $process = new Process($runCommand . ' ' . $file);
        }

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
      'Tests Run'      => $this->_testsRun . '/' . $this->_totalTests,
      'Tests Passed'   => $this->_testsPass,
      'Tests Failed'   => $this->_testsFail,
      ''               => null,
      'Total Duration' => $buildRun->endTime - $buildRun->startTime . ' (seconds)'
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
}
