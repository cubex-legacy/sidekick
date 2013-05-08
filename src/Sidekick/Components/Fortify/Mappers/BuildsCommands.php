<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Data\Attribute;
use Cubex\Mapper\Database\PivotMapper;

class BuildsCommands extends PivotMapper
{
  public $dependencies;

  protected function _configure()
  {
    $this->pivotOn(new Build(), new BuildCommand());
    $this->_attribute("dependencies")->setSerializer(
      Attribute::SERIALIZATION_JSON
    );
  }
}
