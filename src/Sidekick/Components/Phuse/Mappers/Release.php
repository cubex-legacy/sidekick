<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Phuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Release extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;

  public $version;
  public $zipLocation;
  public $packageId;
  public $zipHash;

  protected function _configure()
  {
    $this->_addCompositeAttribute("id", ['packageId', 'version']);
  }

  public function package()
  {
    return $this->belongsTo(new Package());
  }
}
