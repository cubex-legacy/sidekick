<?php
/**
 * Description
 *
 * @author Sam Waters <sam.waters@justdevelop.it>
 */

namespace Sidekick\Components\Notify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class NotifyGroup extends RecordMapper
{
  public $groupName;
  public $groupUsers;

  public function _configure()
  {
    $this->_setSerializer("groupUsers");
  }

  public function getTableName($plural = false)
  {
    return "notify_groups";
  }
}
