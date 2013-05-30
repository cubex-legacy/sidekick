<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Data\Attribute\Attribute;
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

  public $causeBuildFailure = true;

  public $runOnFileSet = false;
  public $fileSetDirectory;
  public $filePattern;


  public $successExitCodes = [0];

  protected function _configure()
  {
    $this->_attribute("successExitCodes")->setSerializer(
      Attribute::SERIALIZATION_JSON
    );
    $this->_attribute("args")->setSerializer(Attribute::SERIALIZATION_JSON);
  }
}
