<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Configure\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Cubex\Sprintf\ParseQuery;

class ConfigurationGroup extends RecordMapper
{
  public $groupName;
  public $entry;
  public $projectId;

  protected function _configure()
  {
    $this->_setRequired('groupName');
    $this->_setRequired('entry');
  }

  public static function getConfigGroupsCount()
  {
    $collection = static::collection();
    $collection->setGroupBy("project_id");
    $collection->setColumns(["project_id", "COUNT(*) AS count"]);
    return $collection->getKeyPair("project_id", "count");
  }
}
