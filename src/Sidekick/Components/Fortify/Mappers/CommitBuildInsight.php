<?php
namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Cassandra\CassandraMapper;
use Sidekick\Components\Fortify\Enums\BuildStatus;

class CommitBuildInsight extends CassandraMapper
{
  public $commit;
  public $branchId;

  protected function _configure()
  {
    $this->_addCompositeAttribute("id", ["branchID", "commit"]);
  }

  public function id()
  {
    return sprintf(
      "%s:%s",
      $this->branchId,
      $this->commit
    );
  }

  public function setProcessState($stage, $alias, BuildStatus $state)
  {
    $key = $this->buildKey('process', $stage, $alias);
    $this->setData($key, $state);
  }

  public function getProcessState($stage, $alias)
  {
    return $this->getData($this->buildKey('process', $stage, $alias));
  }

  /**
   * @param $class     string Calling class e.g. PhpUnit
   * @param $reference string e.g. Open Fix Tags
   * @param $value     string e.g. Counter value "5"
   */
  public function setInsight($class, $reference, $value)
  {
    $key = $this->buildKey('insight', $class, $reference);
    $this->setData($key, $value);
  }

  public function buildKey($source, $alias, $reference)
  {
    return sprintf(
      "%s:%s:%s",
      $source,
      $alias,
      $reference
    );
  }

  public function getInsight($class, $reference)
  {
    return $this->getData($this->buildKey('insight', $class, $reference));
  }
}
