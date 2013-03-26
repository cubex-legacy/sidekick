<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Applications\Diffuse\Mappers;

use Cubex\Mapper\Database\PivotMapper;
use Sidekick\Components\Users\Mappers\User;

class RespositoriesUsers extends PivotMapper
{
  /**
   * @enumclass \Sidekick\Applications\Diffuse\Mappers\UserRole
   */
  public $userRole;
  public $description;

  public function __construct(
    $repositoryId = null, $userId = null, $columns = ['*']
  )
  {
    parent::__construct($repositoryId, $userId, $columns);
  }

  protected function _configure()
  {
    $this->pivotOn(new Repository(), new User());
  }
}
