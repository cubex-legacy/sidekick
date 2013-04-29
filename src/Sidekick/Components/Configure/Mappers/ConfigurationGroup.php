<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Configure\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class ConfigurationGroup extends RecordMapper
{
  public $groupName;
  public $entry;


  protected function _configure()
  {
    $this->_setRequired('groupName');
    $this->_setRequired('entry');
  }

}
