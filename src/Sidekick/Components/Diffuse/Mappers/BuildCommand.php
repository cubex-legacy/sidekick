<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Data\Attribute;
use Cubex\Mapper\Database\RecordMapper;

class BuildCommand extends RecordMapper
{
  /**
   * @length 255
   */
  public $command;
  /**
   * @length 255
   */
  public $args;
  public $name;
  public $description;

  public $successExitCodes = [0];

  protected function _configure()
  {
    $this->_attribute("successExitCodes")->setSerializer(
      Attribute::SERIALIZATION_JSON
    );
    $this->_attribute("args")->setSerializer(Attribute::SERIALIZATION_JSON);
  }
}
