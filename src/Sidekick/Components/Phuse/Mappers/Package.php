<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Phuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Projects\Mappers\Project;

class Package extends RecordMapper
{
  public $name;
  public $description;
  public $author;
  public $projectId;

  public function project()
  {
    return $this->belongsTo(new Project());
  }

  public function releases()
  {
    return $this->hasMany(new Release());
  }
}
