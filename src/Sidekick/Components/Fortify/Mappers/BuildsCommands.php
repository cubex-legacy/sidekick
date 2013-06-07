<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Data\Attribute\Attribute;
use Cubex\Mapper\Database\PivotMapper;

class BuildsCommands extends PivotMapper
{
  public $dependencies;

  protected function _configure()
  {
    $this->pivotOn(new Build(), new Command());
    $this->_attribute("dependencies")->setSerializer(
      Attribute::SERIALIZATION_JSON
    );
  }

  public function command()
  {
    return $this->belongsTo(new Command(), 'command_id');
  }
}
