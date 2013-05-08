<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Data\Attribute;
use Cubex\Mapper\Database\RecordMapper;

class BuildCondition extends RecordMapper
{
  public $conditionClass;
  public $configuration;
  public $causeFailure;

  protected function _configure()
  {
    $this->_attribute("configuration")->setSerializer(
      Attribute::SERIALIZATION_JSON
    );
  }
}
