<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Deployment\Rsync;

use Cubex\Cli\Shell;
use Cubex\Data\Handler\DataHandler;
use Cubex\Helpers\System;
use Cubex\Log\Log;
use Sidekick\Components\Diffuse\Helpers\VersionHelper;
use Sidekick\Components\Diffuse\Mappers\DeploymentStageHost;
use Sidekick\Components\Diffuse\Mappers\Version;
use Sidekick\Deployment\BaseDeploymentService;
use Symfony\Component\Process\Process;

class RsyncService extends BaseDeploymentService
{

  public function deploy()
  {
    $cfg = (new DataHandler())->hydrate($this->_stage->configuration);

    //Base path to deploy code to (remote)
    $remoteBase = $cfg->getStr('deploy_base');
    if($remoteBase === null)
    {
      throw new \Exception("No deploy_base value has been configured.");
    }

    //-z optional (disable for lan sync)
    $options = $cfg->getStr('options', 'z'); //Get options
    if(System::isWindows())
    {
      //--chmod=u=rwX,go=rX
      //--chmod=Du+rwx,og-w,Dog+rx,Fu+rw,Fog+r,F-x
      $options .= " --chmod=Du+rwx,og-w,Dog+rx,Fu+rw,Fog+r,F-x";
    }
    else
    {
      $options .= "p";
    }

    foreach($this->_hosts as $stageHost)
    {
      $host = $stageHost->host();
      if($host->sshPort < 1)
      {
        $host->sshPort = 22;
      }

      //Automatically deploy with hard links
      $cmd = "rsync --rsh='ssh -p $host->sshPort'";
      $cmd .= ' -rltH' . $options . ' --link-dest ';

      //Remote Old Version Path
      $cmd .= build_path_unix(
        $remoteBase,
        $this->_versionPath($this->_previsionVersion())
      );

      //Local Source Path
      $sourcePath = VersionHelper::sourceLocation($this->_version);
      if(CUBEX_ENV === 'development' && Shell::commandExists('cygpath'))
      {
        $sourcePath = trim(shell_exec('cygpath "' . $sourcePath . '"'));
      }
      $cmd .= ' ' . $sourcePath . ' ';

      //Remote Path
      $cmd .= $host->username !== null ? $host->username . '@' : ''; //Username
      $cmd .= $host->getConnPreference() . ':'; //Hostname | IP
      $cmd .= build_path_unix(
        $remoteBase,
        $this->_versionPath($this->_version)
      );

      Log::info(
        "Deploying to " . $host->name . " with '" . $cmd . "'"
      );

      $stageHost->log = $cmd;

      $proc = new Process($cmd);
      $proc->run();
      $stageHost->passed = $proc->getExitCode() === 0;
    }
  }

  protected function _versionPath(Version $v)
  {
    return $v->format() . '/';
  }

  protected function _previsionVersion()
  {
    return new Version();
  }

  public function getConfigurationItems()
  {
    return [
      'deploy_base' => 'Remote base path to deploy code to',
      'options'     => 'Rsync options, defaults to: z'
    ];
  }
}
