<?php
/**
 * Author: oke.ugwu
 * Date: 19/11/13 15:12
 */

namespace Sidekick\Components\Rosetta\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class PendingTranslation extends RecordMapper
{
  /**
   * @varchar
   * @length 150
   */
  public $rowKey;
  public $lang;
  public $projectId;

  protected $_idType = self::ID_COMPOSITE;

  protected function _configure()
  {
    $this->_addCompositeAttribute("id", ["row_key", "lang", "project_id"]);
  }
}
