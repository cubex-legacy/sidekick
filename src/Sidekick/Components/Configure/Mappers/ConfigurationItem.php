<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Configure\Mappers;

use Cubex\Data\Attribute;
use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Configure\Enums\ConfigItemType;

class ConfigurationItem extends RecordMapper
{
  public $key;
  public $value;
  /**
   * @enumclass \Sidekick\Components\Configure\Enums\ConfigItemType
   */
  public $type = ConfigItemType::SIMPLE;
  public $configurationGroupId;

  protected function _configure()
  {
    $this->_attribute('value')->setSerializer(Attribute::SERIALIZATION_JSON);
  }

  public function configGroup()
  {
    return $this->belongsTo(new ConfigurationGroup());
  }
}
