<?php
namespace Sidekick\Components\Fortify;

use Sidekick\Components\Fortify\Mappers\CommitBuildInsight;
use Sidekick\Components\Repository\Mappers\Branch;

abstract class AbstractFortifyElement implements FortifyBuildElement
{
  protected $_basePath = '';
  protected $_scratchPath = '/tmp';
  protected $_config;
  protected $_alias;
  protected $_stage;
  protected $_commitHash;

  /**
   * @var Branch
   */
  protected $_branch;

  /**
   * @var CommitBuildInsight
   */
  protected $_insight;

  public function setBranch(Branch $branch)
  {
    $this->_branch = $branch;
    return $this;
  }

  public function setCommitHash($commitHash)
  {
    $this->_commitHash = $commitHash;
    return $this;
  }

  public function setInsight(CommitBuildInsight $insight)
  {
    $this->_insight = $insight;
    return $this;
  }

  public function setAlias($alias)
  {
    $this->_alias = $alias;
    return $this;
  }

  public function setStage($stage)
  {
    $this->_stage = $stage;
    return $this;
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

  protected function _storeData($key, $data)
  {
    $this->_insight->setProcessData(
      $this->_stage,
      $this->_alias,
      $key,
      $data
    );
  }
}
