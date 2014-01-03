<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Build;

use Cubex\Foundation\Container;
use Sidekick\Components\Fortify\Enums\BuildStatus;
use Sidekick\Components\Fortify\Mappers\CommitBuild;
use Sidekick\Components\Repository\Mappers\Branch;
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
    if(!file_exists($buildPath))
    {
      mkdir($buildPath, 0777, true);
    }

    //Rsync git repo into build directory, using hard links
    //rsync -lrtH repo/path build/path
    $branch   = new Branch($build->branchId);
    $repoPath = $branch->getLocalPath();
    $grabRepo = new Process("rsync -lrtH $repoPath $buildPath");
    $grabRepo->run();
    if($grabRepo->getExitCode() !== 0)
    {
      $this->cleanExit();
      throw new \Exception(
        "Unable to copy the repository from $repoPath to $buildPath"
      );
    }

    //Checkout the build path to the desired commit hash
    //git checkout commithash

    //Queue Up each analyser, as can be processed in parallel
    //Load all static analysis classes

    //Loop over all changed files and pass file path into each analysis class

    //Loop over all analysis classes which run on entire directory

    //Once analysis complete, start build processes e.g. composer

    //Copy vendor directory in from vendor/repos/branch/latest
    //Composer update
    //Push vendor dir back to latest vendor/repos/branch/latest

    //Run build commands

    //Mark the build as complete
    $build->finishedAt = new \DateTime();
    $build->status     = BuildStatus::SUCCESS;
    $build->saveChanges();

    //Revert for testing
    //TODO: REMOVE
    $build->status = BuildStatus::PENDING;
    $build->saveChanges();
  }

  public function cleanExit()
  {
    //Remove build location
  }
}
