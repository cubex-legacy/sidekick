<?php
/**
 * @author  oke.ugwu
 */

namespace Sidekick\Components\Projects\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Users\Mappers\User;

class ProjectUser extends RecordMapper
{
  protected $_idType = self::ID_COMPOSITE;
  protected $_autoTimestamp = false;

  public $userId;
  public $projectId;
  public $roles = [];

  protected function _configure()
  {
    $this->_addCompositeAttribute(
      "id",
      array(
           "projectId",
           "userId",
      )
    );

    $this->_setSerializer('roles');
  }

  public function project()
  {
    return $this->belongsTo(new Project());
  }

  public function user()
  {
    return $this->belongsTo(new User());
  }
}
