<?php
namespace Sidekick\Components\Fortify;

use Sidekick\Components\Repository\Mappers\Branch;

abstract class AbstractFortifyElement implements FortifyBuildElement
{
  protected $_basePath = '';
  protected $_scratchPath = '/tmp';
  protected $_config;
  /**
   * @var Branch
   */
  protected $_branch;

  public function setBranch(Branch $branch)
  {
    $this->_branch = $branch;
  }

  public function setRepoBasePath($basePath)
  {
    $this->_basePath = $basePath;
    return $this;
  }

  public function setScratchDir($scratchPath)
  {
    $this->_scratchPath = $scratchPath;
    return $this;
  }

  public function configure($config)
  {
    $this->_config = $config;
    return $this;
  }
}
