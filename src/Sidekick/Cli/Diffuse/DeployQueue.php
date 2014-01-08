<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Cli\Diffuse;

use Cubex\Cli\CliCommand;
use Cubex\Cli\PidFile;
use Cubex\Cli\Shell;
use Cubex\Facade\Queue;
use Cubex\Log\Log;
use Cubex\Queue\CallableQueueConsumer;
use Cubex\Queue\StdQueue;
use Sidekick\Components\Diffuse\Mappers\Deployment;
use Sidekick\Components\Diffuse\Mappers\Platform;
use Sidekick\Components\Diffuse\Mappers\Version;
use Symfony\Component\Process\Process;

/**
 * Run Build Queue
 */
class DeployQueue extends CliCommand
{
  public $verbose;

  protected $_pidFile;

  protected $_echoLevel = 'debug';

  /**
   * @valuerequired
   */
  public $inactiveSleep = 5;

  public function execute()
  {
    $this->_pidFile = new PidFile();
    Log::debug("Starting Queue Consumer");
    while(true)
    {
      $deployment = Deployment::collection(["pending" => 1])->setLimit(1);
      if($deployment->hasMappers())
      {
        $data = $deployment->first();
        $this->runDeployment($data);
      }
      else
      {
        sleep($this->inactiveSleep);
      }
    }
    Log::debug("Completed Consume");
  }

  public function runDeployment($data)
  {
    $platformId = $data->platformId;
    $versionId  = $data->versionId;

    $version  = new Version($versionId);
    $platform = new Platform($platformId);

    Log::info("Entering Deployment for : " . $version->project()->name);
    Log::info(
      "Version: " . $version->format() . ", Platform: " . $platform->name
    );

    $cwd     = getcwd();
    if(isset($data->id))
    {
      $rawArgs = [
        '--cubex-env=' . CUBEX_ENV,
        'Diffuse.Deploy',
        '--deploymentId=' . $data->id,
        '--echo-level=' . $this->_logger->getEchoLevel(),
        '--log-level=' . $this->_logger->getLogLevel(),
      ];
    }
    else
    {
      $rawArgs = [
        '--cubex-env=' . CUBEX_ENV,
        'Diffuse.Deploy',
        '--versionId=' . $versionId,
        '--platformId=' . $platformId,
        '--echo-level=' . $this->_logger->getEchoLevel(),
        '--log-level=' . $this->_logger->getLogLevel(),
      ];
    }

    if($this->verbose)
    {
      $rawArgs[] = '--verbose';
    }

    if(isset($data->userId) && $data->userId > 0)
    {
      $rawArgs[] = '--userId=' . $data->userId;
    }

    Log::debug("Starting Deployment");

    $command = 'php "' . CUBEX_PROJECT_ROOT . DS . 'cubex" ';
    $command .= implode(' ', $rawArgs);

    Log::debug("Executing: $command");

    $process = new Process($command);
    try
    {
      if($this->verbose)
      {
        $process->run(
          function ($type, $buffer)
          {
            echo $buffer;
          }
        );
      }
      else
      {
        $process->run();
      }
    }
    catch(\Exception $e)
    {
      Log::debug(
        "Failed Deployment (Exit Code: " . $process->getExitCode() . ")"
      );
      Log::error($e->getMessage());
    }

    Log::debug(
      "Executed Deployment (Exit Code: " . $process->getExitCode() . ")"
    );
    chdir($cwd);
    Log::debug("Completed Deployment Run");
    return true;
  }
}
