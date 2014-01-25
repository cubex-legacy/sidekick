<?php
/**
 * ./cubex Fortify.commitBuild -b 1 -c 8f2647210dc5beaf70105a841a2268b6dc1ee3bd --echo-level=debug
 * ./cubex Fortify.Analyser --echo-level=debug
 */

namespace Sidekick\Components\Fortify\Build;

use Cubex\Data\Handler\DataHandler;
use Cubex\Foundation\Container;
use Cubex\Log\Log;
use Sidekick\Components\Fortify\Enums\BuildStatus;
use Sidekick\Components\Fortify\FortifyHelper;
use Sidekick\Components\Fortify\Mappers\BuildAnalysis;
use Sidekick\Components\Fortify\Mappers\CommitBuild;
use Sidekick\Components\Fortify\Mappers\CommitBuildInsight;
use Sidekick\Components\Fortify\Processes\FortifyProcess;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;
use Symfony\Component\Process\Process;
use Symfony\Component\Yaml\Yaml;

class FortifyBuildProcess
{
  protected $_config;

  public function loadYaml($yamlContent)
  {
    $this->_config = Yaml::parse($yamlContent);
  }

  public function runBuild(CommitBuild $build)
  {
    if($this->_config === null)
    {
      throw new \RuntimeException("No configuration has been loaded");
    }

    //Mark the build as running
    $build->startedAt = new \DateTime();
    $build->status    = BuildStatus::RUNNING;
    $build->saveChanges();

    //Create build path
    $buildPath = build_path('fortify', 'b' . $build->branchId, $build->commit);
    $buildBase = Container::config()->get('fortify')->getStr("build_path");
    $buildPath = build_path($buildBase, $buildPath);

    $scratchPath = $buildPath . '.scratch';

    $this->clearPath($buildPath);
    $this->clearPath($scratchPath);

    if(!file_exists($buildPath))
    {
      Log::info("Creating build path @ $buildPath");
      mkdir($buildPath, 0777, true);
    }

    if(!file_exists($scratchPath))
    {
      Log::info("Creating build scrarch path @ $scratchPath");
      mkdir($scratchPath, 0777, true);
    }

    //Rsync git repo into build directory, using hard links
    //rsync -lrtH repo/path build/path
    Log::info("Copying repository into build directory");
    $branch   = new Branch($build->branchId);
    $repoPath = $branch->getLocalPath();

    $command = "rsync -lrtH $repoPath/ $buildPath";
    Log::debug($command);

    $grabRepo = new Process($command);
    $grabRepo->run();
    if($grabRepo->getExitCode() !== 0)
    {
      Log::debug($grabRepo->getErrorOutput());
      $this->clearPath($buildPath);
      $this->clearPath($scratchPath);
      $grabRepo = null;
      throw new \Exception(
        "Unable to copy the repository from $repoPath to $buildPath"
      );
    }
    $grabRepo = null;

    //Checkout the build path to the desired commit hash
    //git checkout commithash
    Log::info("Changing repo to build hash");
    $command = "git checkout " . $build->commit . " --force";
    Log::debug($command);

    $hashUpdate = new Process($command);
    $hashUpdate->setWorkingDirectory($buildPath);
    $hashUpdate->run();
    if($hashUpdate->getExitCode() !== 0)
    {
      Log::debug($hashUpdate->getErrorOutput());
      Log::debug($hashUpdate->getOutput());
      $this->clearPath($buildPath);
      $this->clearPath($scratchPath);
      $hashUpdate = null;
      throw new \Exception(
        "Unable to switch repository to commit hash"
      );
    }

    $sidekickConfig = build_path($buildPath, 'sidekick.yaml');
    if(file_exists($sidekickConfig))
    {
      $this->loadYaml(file_get_contents($sidekickConfig));
    }

    $config = new DataHandler();
    $config->hydrate($this->_config);

    $commit = Commit::loadWhere(
      [
        "branchId"   => $build->branchId,
        "commitHash" => $build->commit,
      ]
    );

    $insight             = new CommitBuildInsight();
    $insight->commit     = $build->commit;
    $insight->branchId   = $build->branchId;
    $insight->status     = $build->status;
    $insight->commitTime = strtotime($commit->committedAt);
    $insight->saveChanges();

    //Queue Up each analyser, as can be processed in parallel
    foreach($config->getArr("analyse", []) as $analyser => $cnf)
    {
      Log::info("Adding $analyser to the queue");
      $analysis                = new BuildAnalysis();
      $analysis->branchId      = $build->branchId;
      $analysis->class         = $analyser;
      $analysis->commitHash    = $build->commit;
      $analysis->configuration = $cnf;
      $analysis->scratchPath   = $scratchPath;
      $analysis->buildPath     = $buildPath;
      $analysis->running       = 0;

      try
      {
        $insight->setProcessState("analyse", $analyser, BuildStatus::PENDING());
        $insight->saveChanges();
        //Analysis must save after insight, to avoid race condition and the
        //insight ending in a pending state
        $analysis->saveChanges();
      }
      catch(\Exception $e)
      {
        //Ignore duplicate entry exceptions
        if($e->getCode() != 1062)
        {
          throw $e;
        }
      }
    }

    //Wait for the DB to catch up, so analysers will appear in the check
    sleep(1);

    //Wait for pending analysers to complete
    while(true)
    {
      $pending = BuildAnalysis::collection(
        [
          "commit_hash" => $build->commit,
          "branch_id"   => $build->branchId
        ]
      )
        ->whereIn("running", [0, 1])
        ->whereLessThan("created_at", (new \DateTime())->format("Y-m-d H:i:s"))
        ->count();

      if($pending == 0)
      {
        break;
      }
      msleep(250);
      Log::debug("Waiting for $pending analysers to complete");
      $pending = null;
    }

    $branch = new Branch($build->branchId);

    //Once analysis complete, start build processes e.g. composer
    Log::info("Running install processes");
    $passedInstall = $this->_runStage(
      $config,
      "install",
      $buildPath,
      $scratchPath,
      $branch,
      $insight
    );

    $build->status = BuildStatus::FAILED;

    if($passedInstall)
    {
      //Run build commands
      Log::info("Running build processes");
      $passedScripts = $this->_runStage(
        $config,
        "script",
        $buildPath,
        $scratchPath,
        $branch,
        $insight
      );

      if($passedScripts)
      {
        Log::info("Build scripts passed");
        $build->status = BuildStatus::SUCCESS;
        $this->_runStage(
          $config,
          "success",
          $buildPath,
          $scratchPath,
          $branch,
          $insight
        );
      }
      else
      {
        Log::info("Build scripts failed");
        $build->status = BuildStatus::FAILED;
        $this->_runStage(
          $config,
          "failed",
          $buildPath,
          $scratchPath,
          $branch,
          $insight
        );
      }
    }

    Log::info("Running uninstall processes");
    $this->_runStage(
      $config,
      "uninstall",
      $buildPath,
      $scratchPath,
      $branch,
      $insight
    );

    $insight->status = $build->status;
    $insight->saveChanges();

    //Mark the build as complete
    $build->finishedAt = new \DateTime();
    $build->saveChanges();

    //TODO: For testing only, REMOVE
    $build->status = BuildStatus::PENDING;
    $build->saveChanges();
  }

  public function clearPath($path)
  {
    try
    {
      $process = new Process("rm -Rf $path");
      $process->run();
    }
    catch(\Exception $e)
    {
    }
  }

  protected function _runStage(
    DataHandler $config, $stage, $buildPath, $scratchPath, Branch $branch,
    CommitBuildInsight $insight
  )
  {
    $processes = $config->getArr($stage, []);
    foreach($processes as $alias => $cfg)
    {
      try
      {
        $class = FortifyHelper::configAliasToClass($alias, $stage);

        if(class_exists($class))
        {
          $process = new $class();
          if($process instanceof FortifyProcess)
          {
            $process->configure($cfg);
            $process->setScratchDir($scratchPath);
            $process->setRepoBasePath($buildPath);
            $process->setBranch($branch);
            $process->setInsight($insight);
            $process->setAlias($alias);
            $process->setStage($stage);

            Log::info("Processing $stage > $alias");

            $passed = $this->_runProcess($process, $stage, $alias, $insight);

            if(!$passed)
            {
              $insight->setProcessState($stage, $alias, BuildStatus::FAILED());
              Log::info("$stage > $alias Failed");
              return false;
            }
            else
            {
              $insight->setProcessState($stage, $alias, BuildStatus::SUCCESS());
            }
            $insight->saveChanges();
          }
        }
      }
      catch(\Exception $e)
      {
        Log::error($e->getMessage());
        return false;
      }
    }
    return true;
  }

  protected function _runProcess(
    FortifyProcess $process, $stage, $alias, CommitBuildInsight $insight
  )
  {
    $passed = false;
    try
    {
      $exitCode = $process->process($stage);
      $passed   = $exitCode === true || $exitCode === 0;
      $insight->setProcessLog($stage, $alias, $process->getLog());
    }
    catch(\Exception $e)
    {
      $insight->setProcessLog($stage, $alias, $e->getMessage());
      Log::error($e->getMessage());
    }
    return $passed;
  }
}
