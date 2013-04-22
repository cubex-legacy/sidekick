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
}
