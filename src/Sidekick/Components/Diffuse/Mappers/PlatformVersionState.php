<?php
/**
 * Created by JetBrains PhpStorm.
 * User: Sam.Waters
 * Date: 19/07/13
 * Time: 16:00
 * To change this template use File | Settings | File Templates.
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Diffuse\Enums\VersionState;

class PlatformVersionState extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;
  public $platformId;
  public $versionId;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Enums\VersionState
   */
  public $state;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["platformId", "versionId"]
    );
  }

  public function states()
  {
    return new VersionState();
  }
}