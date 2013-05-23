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
    $result = self::conn()->getKeyedRows(
      ParseQuery::parse(
        self::conn(),
        "SELECT %C, count(*) FROM %T GROUP BY %C",
        'project_id',
        self::tableName(),
        'project_id'
      )
    );

    return $result;
  }
}
