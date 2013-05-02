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

  public function prepValueOut($input, $type)
  {
    $value = null;
    switch($type)
    {
      case ConfigItemType::SIMPLE:
        $value = $input;
        break;
      case ConfigItemType::MULTI_ITEM:
        $value = implode(',', $input);
        break;
      case ConfigItemType::MULTI_KEYED:
        $value = [];
        foreach($input as $k => $v)
        {
          $value[] = "$k=$v";
        }
        $value = implode(',', $value);
        break;
    }

    return $value;
  }

  public function prepValueIn($input, $type)
  {
    $value = null;
    switch($type)
    {
      case ConfigItemType::SIMPLE:
        $value = $input;
        break;
      case ConfigItemType::MULTI_ITEM:
        $value = explode(',', $input);
        break;
      case ConfigItemType::MULTI_KEYED:
        $value = [];
        $input = explode(',', $input);
        foreach($input as $pair)
        {
          $kv            = explode('=', $pair);
          $value[$kv[0]] = $kv[1];
        }
        break;
    }

    return $value;
  }
}
