<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Phuse\Mappers;

use Cubex\Data\Attribute\Attribute;
use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Projects\Mappers\Project;

class Package extends RecordMapper
{
  public $name;
  public $description;
  /**
   * @datatype text
   */
  public $authors;
  public $version;
  public $license;
  /**
   * @datatype text
   */
  public $require;
  public $projectId;

  protected function _configure()
  {
    $this->_attribute('authors')->setSerializer(Attribute::SERIALIZATION_JSON);
    $this->_attribute('require')->setSerializer(Attribute::SERIALIZATION_JSON);
  }


  public function project()
  {
    return $this->belongsTo(new Project());
  }

  public function releases()
  {
    return $this->hasMany(new Release());
  }
}
