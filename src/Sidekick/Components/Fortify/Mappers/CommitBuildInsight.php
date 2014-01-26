<?php
namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Cassandra\CassandraMapper;
use Sidekick\Components\Fortify\Enums\BuildStatus;

class CommitBuildInsight extends CassandraMapper
{
  public $commit;
  public $branchId;
  public $status;
  public $commitTime;

  public function id()
  {
    return sprintf("%s:%s", $this->branchId, $this->commit);
  }

  public function buildKey($source, $alias, $reference, $key = null)
  {
    $format = trim(str_repeat("%s:", func_num_args()), ':');
    return vsprintf($format, func_get_args());
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

  public function setProcessLog($stage, $alias, $log)
  {
    $key = $this->buildKey('log', $stage, $alias);
    $this->setData($key, $log);
  }

  public function getProcessLog($stage, $alias)
  {
    return $this->getData($this->buildKey('log', $stage, $alias));
  }

  public function setProcessData($stage, $alias, $dataKey, $data)
  {
    $key = $this->buildKey('x-data', $stage, $alias, $dataKey);
    $this->setData($key, $data);
  }

  public function getProcessData($stage, $alias, $dataKey)
  {
    return $this->getData($this->buildKey('x-data', $stage, $alias, $dataKey));
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
    $timeline = new InsightTimeline();
    $timeline->setId("$this->branchId:$class:$reference");
    $timeline->setData($this->commitTime, $value);
    $timeline->saveChanges();
  }

  public function getInsight($class, $reference)
  {
    $key = $this->buildKey('insight', $class, $reference);
    if($this->attributeExists($key))
    {
      return $this->getData($key);
    }
    return null;
  }
}
