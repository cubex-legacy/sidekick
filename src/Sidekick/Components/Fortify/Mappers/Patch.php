<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Fortify\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class Patch extends RecordMapper
{
  public $author;
  /**
   * @datatype BLOB
   */
  public $patch;
  public $name;
  public $filename;
  /**
   * @datatype tinyint
   */
  public $leadingSlashes = 1;
}
