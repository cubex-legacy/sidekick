<?php
namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;
use Cubex\Log\Log;
use Sidekick\Components\Fortify\Analysers\FortifyAnalyser;
use Sidekick\Components\Fortify\Enums\BuildStatus;
use Sidekick\Components\Fortify\FortifyHelper;
use Sidekick\Components\Fortify\Mappers\BuildAnalysis;
use Sidekick\Components\Fortify\Mappers\CommitBuildInsight;
use Sidekick\Components\Repository\Mappers\Branch;
use Sidekick\Components\Repository\Mappers\Commit;

class Analyser extends CliCommand
{
  /**
   * @valuerequire
   */
  public $sleep = 5;

  /**
   * @return int
   */
  public function execute()
  {
    Log::info("Starting build analysis consumer");
    while(true)
    {
      $analysers = BuildAnalysis::collection(['running' => 0])->setLimit(0, 1);
      if($analysers->get()->hasMappers())
      {
        $analyser = $analysers->first();
        /**
         * @var $analyser BuildAnalysis
         */

        //Remove the analyser from the queue
        $analyser->running = 1;
        $analyser->saveChanges();

        $insight           = new CommitBuildInsight();
        $insight->commit   = $analyser->commitHash;
        $insight->branchId = $analyser->branchId;

        try
        {
          $class = FortifyHelper::configAliasToClass(
            $analyser->class,
            'analyse'
          );
        }
        catch(\Exception $e)
        {
          $analyser->error   = $e->getMessage();
          $analyser->running = 2;
          $analyser->saveChanges();
          $insight->setProcessState(
            "analyse",
            $analyser->class,
            BuildStatus::FAILED()
          );
          $insight->saveChanges();
          continue;
        }

        $passed = false;

        if(class_exists($class))
        {
          try
          {
            $analyse = new $class;
            if($analyse instanceof FortifyAnalyser)
            {
              $commit = Commit::loadWhere(
                [
                  "commit_hash" => $analyser->commitHash,
                  "branch_id"   => $analyser->branchId
                ]
              );

              if($commit === null || !($commit instanceof Commit))
              {
                throw new \Exception("Unable to find the commit");
              }

              Log::debug("$class is analysing");
              $analyse->setRepoBasePath($analyser->buildPath);
              $analyse->setScratchDir($analyser->scratchPath);
              $analyse->setBranch(new Branch($analyser->branchId));
              $analyse->configure($analyser->configuration);
              $passed = $analyse->analyse($commit);
            }
            else
            {
              $analyser->error = "$class is not a type of FortifyAnalyser";
              Log::error($analyser->error);
            }
          }
          catch(\Exception $e)
          {
            $analyser->error = $e->getCode() . ') ' . $e->getMessage();
            Log::error($analyser->error);
          }
        }
        else
        {
          $analyser->error = "The class $class does not exist";
          Log::error($analyser->error);
        }

        if($passed)
        {
          Log::info("Analysis complete");
          $insight->setProcessState(
            "analyse",
            $analyser->class,
            BuildStatus::SUCCESS()
          );
          $insight->saveChanges();
          $analyser->delete();
        }
        else
        {
          $insight->setProcessState(
            "analyse",
            $analyser->class,
            BuildStatus::FAILED()
          );
          $insight->setInsight(
            $analyser->class,
            'error_message',
            $analyser->error
          );
          $insight->saveChanges();

          $analyser->running = 2;
          $analyser->saveChanges();

          Log::error("Failed to analyse");
        }
      }
      else
      {
        Log::debug("Sleeping for $this->sleep seconds.");
        sleep($this->sleep);
      }
    }
    Log::info("Finished consuming");
  }

  protected function _addFiles(
    BuildAnalysis $analyseRequest, FortifyAnalyser $analyser
  )
  {
  }
}
