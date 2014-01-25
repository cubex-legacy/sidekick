<?php
namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;
use Cubex\Log\Log;
use Sidekick\Components\Fortify\Build\FortifyBuildProcess;
use Sidekick\Components\Fortify\Enums\BuildStatus;
use Sidekick\Components\Fortify\Mappers\CommitBuild as CommitBuildMapper;

class CommitBuild extends CliCommand
{
  /**
   * @valuerequired
   */
  public $seconds = 5;

  public function execute()
  {
    while(true)
    {
      $builds = CommitBuildMapper::collection(
        ["status" => BuildStatus::PENDING]
      )
        ->setLimit(0, 1)
        ->whereLessThan("createdAt", (new \DateTime())->format("Y-m-d H:i:s"))
        ->setOrderBy("createdAt", 'ASC');

      if($builds->hasMappers())
      {
        $build = $builds->first();
        if($build->status !== BuildStatus::PENDING)
        {
          throw new \RuntimeException(
            "This build is currently in a " . $build->status . ' state, ' .
            'therefore cannot be executed.'
          );
        }

        $buildProcess = new FortifyBuildProcess();
        $buildProcess->loadYaml(
          file_get_contents(
            build_path(CUBEX_PROJECT_ROOT, 'conf/default.yaml')
          )
        );

        $buildProcess->runBuild($build);
      }
      else
      {
        Log::debug("Waiting $this->seconds seconds for next build");
        sleep($this->seconds);
      }

      $build  = null;
      $builds = null;
    }
  }
}
