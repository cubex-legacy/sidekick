<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Phuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Release extends RecordMapper
{
  public $version;
  public $zipLocation;
  public $packageId;

  public function package()
  {
    return $this->belongsTo(new Package());
  }
}
