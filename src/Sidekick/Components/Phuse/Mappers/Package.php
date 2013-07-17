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
  public $vendor;
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
  /**
   * @datatype text
   */
  public $rawComposer;

  protected function _configure()
  {
    $this->_setSerializer('authors');
    $this->_setSerializer('require');
    $this->_setSerializer('rawComposer');
  }

  public function project()
  {
    return $this->belongsTo(new Project());
  }

  public function releases()
  {
    return $this->hasMany(new Release());
  }

  public function release()
  {
    return Release::collection(['package_id' => $this->id()])->setOrderBy(
             'created_at',
             'DESC'
           )->first();
  }
}
