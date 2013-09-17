<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment\Commander;

use Cubex\Data\Handler\DataHandler;
use Cubex\Log\Log;
use Sidekick\Deployment\BaseDeploymentService;
use Symfony\Component\Process\Process;

class CommanderService extends BaseDeploymentService
{
  public function deploy()
  {
    $cfg       = (new DataHandler())->hydrate($this->_stage->configuration);
    $command   = $cfg->getStr("command", null);
    $exitCodes = $cfg->getArr("exit_codes", [0]);

    //ssh {username}@{server} "ln -fns /home/cubex.nginx/{version_format} /home/cubex.nginx/stage; super restartphp"
    //ssh {username}@{server} -p {sshport} "ln -fns /home/cubex.nginx/{version_format} /home/cubex.nginx/stage; super restartphp

    if($command === null)
    {
      throw new \Exception("No command value has been configured.");
    }

    foreach($this->_hosts as $stageHost)
    {
      $host = $stageHost->host();
      \Log::info("Running command on " . $host->name);

      $cmd = str_replace(
        [
        '{username}',
        '{server}',
        '{hostname}',
        '{sshport}',
        '{ipv4}',
        '{ipv6}',
        '{version_format}',
        '{version_major}',
        '{version_minor}',
        '{version_build}',
        '{version_revision}',
        '{version_type}',
        '{version_build_id}',
        '{version_project_id}',
        '{version_repo_id}',
        '{version_from_commit_hash}',
        '{version_to_commit_hash}',
        '{version_change_log}',
        ],
        [
        $host->username,
        $host->getConnPreference(),
        $host->hostname,
        $host->sshPort,
        $host->ipv4,
        $host->ipv6,
        $this->_version->format(),
        $this->_version->major,
        $this->_version->minor,
        $this->_version->build,
        $this->_version->revision,
        $this->_version->type,
        $this->_version->buildId,
        $this->_version->projectId,
        $this->_version->repoId,
        $this->_version->fromCommitHash,
        $this->_version->toCommitHash,
        $this->_version->changeLog,
        ],
        $command
      );

      Log::info($cmd);

      $process = new Process($cmd);
      $process->run();

      $stageHost->log = $process->getOutput();

      Log::info($cmd);
      Log::debug($process->getOutput());

      $stageHost->passed = in_array($process->getExitCode(), $exitCodes);
    }
  }

  public static function getConfigurationItems()
  {
    return [
      'command'    => 'Bash command to execute',
      'exit_codes' => 'Passing Exit codes, defaults to: 0'
    ];
  }
}
