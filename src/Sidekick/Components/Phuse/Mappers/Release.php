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
  public $name;
  public $description;
  /**
   * @datatype text
   */
  public $authors;
  /**
   * @datatype text
   */
  public $require;
  public $license;
  public $vendor;
  public $library;

  protected function _configure()
  {
    $this->_addCompositeAttribute("id", ['packageId', 'version']);
    $this->_setSerializer('authors');
    $this->_setSerializer('require');
  }

  public function package()
  {
    return $this->belongsTo(new Package());
  }
}
