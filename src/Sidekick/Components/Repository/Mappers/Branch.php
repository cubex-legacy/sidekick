<?php
/**
 * @author Brooke Bryan @bajbnet
 */

namespace Sidekick\Components\Repository\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Branch extends RecordMapper
{
  public $repositoryId;
  public $branch;

  public $name;
  public $description;

  /**
   * @datatype smallint
   */
  public $majorVersion;
  /**
   * @datatype smallint
   */
  public $minorVersion;

  public $commitBuildId;

  /**
   * @return Repository
   */
  public function repository()
  {
    return $this->belongsTo(new Repository());
  }

  public function getLocalPath()
  {
    return build_path($this->repository()->localpath, $this->branch);
  }

  public function commits()
  {
    return $this->hasMany(new Commit());
  }
} 