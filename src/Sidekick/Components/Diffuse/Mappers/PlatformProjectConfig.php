<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class PlatformProjectConfig extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;

  public $projectId;
  public $platformId;

  public $cookieName = 'CUBEX_VERSION';
  public $testUrl = 'http://www.domain.tld/';

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["platformId", "projectId"]
    );
  }
}
