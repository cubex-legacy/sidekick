<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Enums\Consistency;
use Sidekick\Components\Users\Enums\UserRole;

class ApprovalConfiguration extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;
  public $platformId;
  public $projectId;
  /**
   * @enumclass \Sidekick\Components\Users\Enums\UserRole
   */
  public $role;
  /**
   * @enumclass \Sidekick\Components\Enums\Consistency
   */
  public $consistencyLevel;
  public $required;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      ["platformId", "projectId", "role"]
    );
  }

  public function consistencyLevels()
  {
    return new Consistency();
  }

  public function roles()
  {
    return new UserRole();
  }
}
