<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;

class ApprovalConfiguration extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;

  public $projectId;
  public $role;

  public $consistencyLevel;
  public $required;

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      array(
           "projectId",
           "role",
      )
    );
  }
}
