<?php
namespace Sidekick\Cli\Fortify;

use Cubex\Cli\CliCommand;
use Sidekick\Components\Fortify\Build\FortifyBuildProcess;
use Sidekick\Components\Fortify\Enums\BuildStatus;
use Sidekick\Components\Fortify\Mappers\CommitBuild as CommitBuildMapper;

class CommitBuild extends CliCommand
{
  /**
   * @valuerequired
   * @required
   */
  public $branchId;
  /**
   * @valuerequired
   * @required
   */
  public $commitHash;

  public function execute()
  {
    $build = new CommitBuildMapper([$this->branchId, $this->commitHash]);
    if(!$build->exists())
    {
      throw new \RuntimeException("The build specified does not exist");
    }

    if($build->status !== BuildStatus::PENDING)
    {
      /*throw new \RuntimeException(
        "This build is currently in a " . $build->status . ' state, ' .
        'therefore cannot be executed.'
      );*/
    }

    $buildProcess = new FortifyBuildProcess();
    $buildProcess->loadYaml(
      file_get_contents(
        build_path(CUBEX_PROJECT_ROOT, 'conf/default.yaml')
      )
    );

    $buildProcess->runBuild($build);
  }
}
