<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Data\Validator\Validator;
use Cubex\Mapper\Database\RecordMapper;

class Build extends RecordMapper
{
  public $name;
  public $description;
  /**
   * @enumclass \Sidekick\Components\Fortify\Enum\BuildLevel
   */
  public $buildLevel;
  public $sourceDirectory = 'sourcecode/';

  protected function _configure()
  {
    $this->_attribute("name")->addValidator(Validator::VALIDATE_NOTEMPTY);
    $this->_attribute("buildLevel")->addValidator(Validator::VALIDATE_NOTEMPTY);
    $this->_attribute("sourceDirectory")->addValidator(Validator::VALIDATE_NOTEMPTY);
  }
}
