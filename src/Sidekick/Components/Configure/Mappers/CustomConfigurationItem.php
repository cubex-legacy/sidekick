<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Components\Configure\Mappers;

use Cubex\Data\Attribute;
use Cubex\Mapper\Database\RecordMapper;

class CustomConfigurationItem extends RecordMapper
{
  public $itemId;
  public $value;

  protected function _configure()
  {
    $this->_attribute('value')->setSerializer(Attribute::SERIALIZATION_JSON);
  }
}