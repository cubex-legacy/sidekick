<?php
/**
 * @author  brooke.bryan
 */

namespace Sidekick\Components\Diffuse\Mappers;

use Cubex\Mapper\Database\RecordMapper;
use Sidekick\Components\Users\Mappers\User;

class Action extends RecordMapper
{
  public $versionId;
  public $userId;
  /**
   * @enumclass \Sidekick\Components\Diffuse\Enums\ActionType
   */
  public $actionType;
  /**
   * @datatype text
   */
  public $comment;


  /**
   * @return User
   */
  public function user()
  {
    return new User($this->userId);
  }
}
