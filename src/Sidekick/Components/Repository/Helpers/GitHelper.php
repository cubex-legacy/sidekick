<?php
namespace Sidekick\Components\Repository\Helpers;

use Sidekick\Components\Fortify\Mappers\BuildLog;
use Sidekick\Components\Repository\Exceptions\InvalidRepositoryException;
use Sidekick\Components\Repository\Exceptions\RepositoryNotFoundException;
use Symfony\Component\Process\Process;

class GitHelper
{
  const PROCESS_TIMEOUT = 600;
  const PROCESS_IDLE_TIMEOUT = 600;

  /**
   * Get a clean copy of a repo by rsyncing a locally-cached clone
   * This is better for our deployments than using a fresh clone because
   * it prevents the file timestamps from changing on every build
   *
   * @param string   $localpath
   * @param string   $branch
   * @param string   $buildSourceDir
   * @param BuildLog $log
   *
   * @throws \Exception
   */
  public static function getCleanRepo(
    $localPath, $branch, $buildSourceDir, BuildLog $log = null
  )
  {
    $callback  = $log ? [$log, 'writeBuffer'] : null;

    static::checkoutBranch($branch, $localPath, $log);

    if(!file_exists($buildSourceDir))
    {
      mkdir($buildSourceDir, 0755, true);
    }
    $buildSourceDir = realpath($buildSourceDir);

    $process = new Process(
      'rsync -a --exclude \'.git\' ' . escapeshellarg(
        rtrim($localPath, '/') . '/'
      )
      . ' ' . escapeshellarg(rtrim($buildSourceDir, '/') . '/')
    );
    $process->run($callback);

    $retval = $process->getExitCode();
    if($retval != 0)
    {
      throw new \Exception(
        'Error copying cached repository from ' . $localPath . ' to '
        . $buildSourceDir
      );
    }
  }

  /**
   * Clone or pull a repo
   *
   * @param          $remoteUrl
   * @param          $branch
   * @param          $localPath
   * @param BuildLog $log
   *
   * @throws InvalidRepositoryException
   * @throws \Exception
   */
  public static function checkoutBranch($branch, $localPath, BuildLog $log)
  {
    if(file_exists($localPath))
    {
      $checkoutProc = new Process(
        'git checkout ' . escapeshellarg($branch),
        $localPath
      );
      $checkoutProc->run([$log, 'writeBuffer']);
      $checkoutRet = $checkoutProc->getExitCode();
      if($checkoutRet != 0)
      {
        throw new \Exception(
          'Error checking out branch ' . $branch . ' in repository at '
          . $localPath
        );
      }
    }
    else
    {
      throw new \Exception(
        'Cached Repository does not exist ' . $localPath
      );
    }
  }

  /**
   * Clone or pull a repo
   *
   * @param          $remoteUrl
   * @param          $branch
   * @param          $localPath
   * @param BuildLog $log
   *
   * @throws InvalidRepositoryException
   * @throws \Exception
   */
  public static function getOrUpdateRepo(
    $remoteUrl, $branch, $localPath, BuildLog $log = null
  )
  {
    $parentDir = dirname($localPath);
    if(!file_exists($parentDir))
    {
      mkdir($parentDir, 0755, true);
    }
    $localPath = build_path(realpath($parentDir), basename($localPath));
    $callback  = $log ? [$log, 'writeBuffer'] : null;

    $exists = false;
    try
    {
      $currentRemote = static::_getOriginUrl($localPath);
      if($currentRemote != $remoteUrl)
      {
        static::_setOriginUrl($localPath, $remoteUrl);
      }
      $exists = true;
    }
    catch(RepositoryNotFoundException $e)
    {
    }

    if($exists)
    {
      $pullProc = new Process('git pull --rebase', $localPath);
      $pullProc->run($callback);
      $pullRet = $pullProc->getExitCode();
      if($pullRet != 0)
      {
        if($log)
        {
          $log->exitCode = $pullRet;
        }
        throw new \Exception(
          'Error pulling repository ' . $remoteUrl . ' into ' . $localPath
        );
      }

      $checkoutProc = new Process(
        'git checkout ' . escapeshellarg($branch),
        $localPath
      );
      $checkoutProc->run($callback);
      $checkoutRet = $checkoutProc->getExitCode();
      if($checkoutRet != 0)
      {
        if($log)
        {
          $log->exitCode = $checkoutRet;
        }
        throw new \Exception(
          'Error checking out branch ' . $branch . ' in repository at '
          . $localPath
        );
      }
      if($log)
      {
        $log->exitCode = 0;
      }
    }
    else
    {
      $process = new Process(
        'git clone -v ' . escapeshellarg($remoteUrl)
        . ' --branch ' . escapeshellarg($branch)
        . ' ' . escapeshellarg($localPath)
      );
      $process->setTimeout(static::PROCESS_TIMEOUT);
      $process->setIdleTimeout(static::PROCESS_IDLE_TIMEOUT);

      $process->run($callback);
      $retval = $process->getExitCode();
      if($log)
      {
        $log->exitCode = $retval;
      }
    }
  }

  /**
   * Get the origin URL of a local git repo
   *
   * @param string $localRepoPath
   *
   * @return string
   * @throws \Exception
   */
  private static function _getOriginUrl($localRepoPath)
  {
    if(!file_exists($localRepoPath))
    {
      throw new RepositoryNotFoundException($localRepoPath . ' does not exist');
    }
    if(!file_exists($localRepoPath . '/.git'))
    {
      throw new InvalidRepositoryException(
        $localRepoPath . ' exists but is not a git repository'
      );
    }
    $process = new Process(
      'git config remote.origin.url', $localRepoPath
    );
    $process->run();
    if($process->getExitCode() == 0)
    {
      return trim($process->getOutput());
    }
    else
    {
      throw new \Exception(
        'Error getting origin URL for the repository at ' . $localRepoPath
      );
    }
  }

  /**
   * Change the origin URL of a git repo
   *
   * @param $localPath
   * @param $url
   *
   * @throws \Exception
   */
  private static function _setOriginUrl($localPath, $url)
  {
    $process = new Process(
      'git remote set-url origin ' . escapeshellarg($url), $localPath
    );
    $process->run();
    if($process->getExitCode() != 0)
    {
      throw new \Exception(
        'Error setting origin URL to "' . $url . '" for repository at '
        . $localPath
      );
    }
  }

  /**
   * Get the path to a repository's locally cached version
   *
   * @param string $repoUrl
   *
   * @return string
   */
  private static function _getCachePath($repoUrl)
  {
    $parts  = explode(':', $repoUrl);
    $subdir = count($parts) > 1 ? $parts[1] : $parts[0];

    if(substr($subdir, -4) == '.git')
    {
      $subdir = substr($subdir, 0, -4);
    }

    return build_path('/sidekick/repos', $subdir);
  }
}
